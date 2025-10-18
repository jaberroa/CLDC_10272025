import { serve } from "https://deno.land/std@0.168.0/http/server.ts"
import { createClient } from 'https://esm.sh/@supabase/supabase-js@2'

const corsHeaders = {
  'Access-Control-Allow-Origin': '*',
  'Access-Control-Allow-Headers': 'authorization, x-client-info, apikey, content-type',
}

serve(async (req) => {
  // Handle CORS preflight requests
  if (req.method === 'OPTIONS') {
    return new Response(null, { headers: corsHeaders });
  }

  try {
    const supabaseClient = createClient(
      Deno.env.get('SUPABASE_URL') ?? '',
      Deno.env.get('SUPABASE_ANON_KEY') ?? '',
      {
        global: {
          headers: { Authorization: req.headers.get('Authorization')! },
        },
      }
    )

    const { type } = await req.json()

    let responseData = {}

    switch (type) {
      case 'miembros_estadisticas':
        responseData = await getMiembrosEstadisticas(supabaseClient)
        break
      case 'organizaciones_estadisticas':
        responseData = await getOrganizacionesEstadisticas(supabaseClient)
        break
      case 'financieras_estadisticas':
        responseData = await getFinancierasEstadisticas(supabaseClient)
        break
      case 'proceso_csv':
        const { csvContent } = await req.json()
        responseData = await procesarCSV(csvContent)
        break
      default:
        throw new Error('Tipo de reporte no válido')
    }

    return new Response(
      JSON.stringify(responseData),
      {
        headers: { ...corsHeaders, 'Content-Type': 'application/json' },
        status: 200,
      },
    )

  } catch (error) {
    console.error('Error en reporte-generator:', error)
    return new Response(
      JSON.stringify({ error: error.message }),
      {
        headers: { ...corsHeaders, 'Content-Type': 'application/json' },
        status: 400,
      },
    )
  }
})

async function getMiembrosEstadisticas(supabase: any) {
  const { data: miembros, error } = await supabase
    .from('miembros')
    .select(`
      id,
      estado_membresia,
      fecha_ingreso,
      organizacion_id,
      organizaciones:organizacion_id (
        nombre,
        provincia
      )
    `)

  if (error) throw error

  const stats = {
    total: miembros.length,
    por_estado: {},
    por_provincia: {},
    crecimiento_mensual: {}
  }

  // Estadísticas por estado
  miembros.forEach(m => {
    stats.por_estado[m.estado_membresia] = (stats.por_estado[m.estado_membresia] || 0) + 1
  })

  // Estadísticas por provincia
  miembros.forEach(m => {
    const provincia = m.organizaciones?.provincia || 'Sin provincia'
    stats.por_provincia[provincia] = (stats.por_provincia[provincia] || 0) + 1
  })

  // Crecimiento mensual
  miembros.forEach(m => {
    const fecha = new Date(m.fecha_ingreso)
    const mesAno = `${fecha.getFullYear()}-${(fecha.getMonth() + 1).toString().padStart(2, '0')}`
    stats.crecimiento_mensual[mesAno] = (stats.crecimiento_mensual[mesAno] || 0) + 1
  })

  return stats
}

async function getOrganizacionesEstadisticas(supabase: any) {
  const { data: organizaciones, error } = await supabase
    .from('organizaciones')
    .select('*')

  if (error) throw error

  const stats = {
    total: organizaciones.length,
    por_tipo: {},
    por_estado: {},
    por_provincia: {}
  }

  organizaciones.forEach(org => {
    stats.por_tipo[org.tipo] = (stats.por_tipo[org.tipo] || 0) + 1
    stats.por_estado[org.estado_adecuacion] = (stats.por_estado[org.estado_adecuacion] || 0) + 1
    stats.por_provincia[org.provincia] = (stats.por_provincia[org.provincia] || 0) + 1
  })

  return stats
}

async function getFinancierasEstadisticas(supabase: any) {
  const { data: transacciones, error } = await supabase
    .from('transacciones_financieras')
    .select('*')

  if (error) throw error

  const stats = {
    total_transacciones: transacciones.length,
    total_ingresos: 0,
    total_egresos: 0,
    por_categoria: {},
    por_mes: {}
  }

  transacciones.forEach(t => {
    if (t.tipo === 'ingreso') {
      stats.total_ingresos += parseFloat(t.monto)
    } else {
      stats.total_egresos += parseFloat(t.monto)
    }

    stats.por_categoria[t.categoria] = (stats.por_categoria[t.categoria] || 0) + parseFloat(t.monto)

    const fecha = new Date(t.fecha)
    const mesAno = `${fecha.getFullYear()}-${(fecha.getMonth() + 1).toString().padStart(2, '0')}`
    stats.por_mes[mesAno] = (stats.por_mes[mesAno] || 0) + parseFloat(t.monto)
  })

  return stats
}

function procesarCSV(csvContent: string) {
  const lines = csvContent.split('\n').filter(line => line.trim().length > 0)
  
  if (lines.length === 0) {
    return { miembros: 0, errores: ['Archivo CSV vacío'] }
  }

  const headers = lines[0].split(',').map(h => h.trim().toLowerCase())
  const miembros = []
  const errores = []

  // Validar headers esperados
  const requiredHeaders = ['nombre', 'cedula', 'email']
  const missingHeaders = requiredHeaders.filter(h => !headers.includes(h))
  
  if (missingHeaders.length > 0) {
    errores.push(`Headers faltantes: ${missingHeaders.join(', ')}`)
  }

  // Procesar filas de datos
  for (let i = 1; i < lines.length; i++) {
    const row = lines[i].split(',').map(cell => cell.trim())
    
    if (row.length !== headers.length) {
      errores.push(`Fila ${i + 1}: Número incorrecto de columnas`)
      continue
    }

    const miembro = {}
    headers.forEach((header, index) => {
      miembro[header] = row[index]
    })

    // Validaciones básicas
    if (!miembro.nombre || miembro.nombre.length < 2) {
      errores.push(`Fila ${i + 1}: Nombre inválido`)
    }

    if (!miembro.email || !miembro.email.includes('@')) {
      errores.push(`Fila ${i + 1}: Email inválido`)
    }

    if (!miembro.cedula || miembro.cedula.length < 10) {
      errores.push(`Fila ${i + 1}: Cédula inválida`)
    }

    if (errores.length === 0 || errores.length < 5) { // Solo agregar si no hay muchos errores
      miembros.push(miembro)
    }
  }

  return {
    miembros: miembros.length,
    errores: errores.slice(0, 10), // Máximo 10 errores
    datos: miembros.slice(0, 100), // Máximo 100 registros de muestra
    valido: errores.length === 0
  }
}