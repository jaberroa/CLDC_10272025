import { SEO } from "@/components/seo/SEO";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Textarea } from "@/components/ui/textarea";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Badge } from "@/components/ui/badge";
import { Progress } from "@/components/ui/progress";
import { useState, useEffect } from "react";
import { supabase } from "@/integrations/supabase/client";
import { toast } from "@/hooks/use-toast";
import { useAuth } from "@/components/auth/AuthProvider";
import { ArrowLeft, Users, FileText, BarChart3, UserPlus, Building, Calendar, TrendingUp, MapPin, Briefcase, School, GraduationCap } from "lucide-react";
import { Link } from "react-router-dom";

const RegistroAdecuacion = () => {
  const { user } = useAuth();
  
  // Seccional registration state
  const [nombre, setNombre] = useState("");
  const [directiva, setDirectiva] = useState("");
  const [miembrosFile, setMiembrosFile] = useState<File | null>(null);
  const [actasFiles, setActasFiles] = useState<File[]>([]);
  const [submitting, setSubmitting] = useState(false);
  const [ultimoResumen, setUltimoResumen] = useState<{ miembros: number; ok: boolean } | null>(null);
  
  // Individual member registration state
  const [memberData, setMemberData] = useState({
    nombreCompleto: "",
    cedula: "",
    email: "",
    telefono: "",
    direccion: "",
    profesion: "",
    fechaNacimiento: "",
    organizacionId: "",
    tipoMembresia: "activo" as const
  });
  const [organizaciones, setOrganizaciones] = useState<any[]>([]);
  const [registeringMember, setRegisteringMember] = useState(false);
  
  // Gremio registration state
  const [gremioData, setGremioData] = useState({
    nombre: "",
    codigo: "",
    provincia: "",
    ciudad: "",
    direccion: "",
    telefono: "",
    email: "",
    presidente: "",
    secretario: "",
    tesorero: ""
  });
  const [registeringGremio, setRegisteringGremio] = useState(false);
  
  // Asociación registration state
  const [asociacionData, setAsociacionData] = useState({
    nombre: "",
    codigo: "",
    provincia: "",
    ciudad: "",
    direccion: "",
    telefono: "",
    email: "",
    tipoAsociacion: "profesional",
    presidente: "",
    secretario: ""
  });
  const [registeringAsociacion, setRegisteringAsociacion] = useState(false);
  
  // Student registration state
  const [estudianteData, setEstudianteData] = useState({
    nombreCompleto: "",
    cedula: "",
    email: "",
    telefono: "",
    universidad: "",
    carrera: "",
    semestre: "",
    numeroEstudiante: "",
    fechaNacimiento: "",
    direccion: ""
  });
  const [registeringEstudiante, setRegisteringEstudiante] = useState(false);
  
  // Statistics state
  const [stats, setStats] = useState({
    totalMiembros: 0,
    miembrosActivos: 0,
    organizacionesTotal: 0,
    nuevosEsteMes: 0,
    distribucionProvincias: [] as { provincia: string; count: number }[],
    crecimientoMensual: [] as { mes: string; cantidad: number }[]
  });
  const [loadingStats, setLoadingStats] = useState(false);

  useEffect(() => {
    loadOrganizaciones();
    loadStatistics();
  }, []);

  const loadOrganizaciones = async () => {
    try {
      const { data, error } = await supabase
        .from('organizaciones')
        .select('id, nombre, codigo, tipo')
        .order('nombre');
      
      if (error) throw error;
      setOrganizaciones(data || []);
    } catch (err: any) {
      console.error('Error loading organizations:', err);
    }
  };

  const loadStatistics = async () => {
    setLoadingStats(true);
    try {
      // Get basic stats
      const { data: dashStats, error: dashError } = await supabase
        .rpc('get_dashboard_stats');
      
      if (dashError) throw dashError;
      
      if (dashStats && dashStats[0]) {
        setStats(prev => ({
          ...prev,
          miembrosActivos: dashStats[0].total_miembros_activos || 0,
          organizacionesTotal: dashStats[0].total_organizaciones || 0
        }));
      }

      // Get total members
      const { count: totalCount } = await supabase
        .from('miembros')
        .select('*', { count: 'exact', head: true });
      
      // Get new members this month
      const startOfMonth = new Date();
      startOfMonth.setDate(1);
      startOfMonth.setHours(0, 0, 0, 0);
      
      const { count: newThisMonth } = await supabase
        .from('miembros')
        .select('*', { count: 'exact', head: true })
        .gte('created_at', startOfMonth.toISOString());

      setStats(prev => ({
        ...prev,
        totalMiembros: totalCount || 0,
        nuevosEsteMes: newThisMonth || 0
      }));

      // Get province distribution
      const { data: miembros } = await supabase
        .from('miembros')
        .select(`
          id,
          organizaciones!inner(provincia)
        `)
        .not('organizaciones.provincia', 'is', null);

      if (miembros) {
        const provinciaCount: { [key: string]: number } = {};
        miembros.forEach((m: any) => {
          const provincia = m.organizaciones?.provincia;
          if (provincia) {
            provinciaCount[provincia] = (provinciaCount[provincia] || 0) + 1;
          }
        });
        
        const distribucionProvincias = Object.entries(provinciaCount)
          .map(([provincia, count]) => ({ provincia, count }))
          .sort((a, b) => b.count - a.count);
        
        setStats(prev => ({
          ...prev,
          distribucionProvincias
        }));
      }

    } catch (err: any) {
      console.error('Error loading statistics:', err);
    } finally {
      setLoadingStats(false);
    }
  };

  const slugify = (s: string) =>
    s
      .normalize("NFD")
      .replace(/[\u0300-\u036f]/g, "")
      .toLowerCase()
      .trim()
      .replace(/[^a-z0-9]+/g, "-")
      .replace(/(^-|-$)/g, "");

  const readFileText = (file: File) =>
    new Promise<string>((resolve, reject) => {
      const reader = new FileReader();
      reader.onload = () => resolve(String(reader.result || ""));
      reader.onerror = reject;
      reader.readAsText(file);
    });

  const contarMiembrosDesdeCSV = (csvText: string) => {
    const lines = csvText
      .split(/\r?\n/)
      .map((l) => l.trim())
      .filter((l) => l.length > 0);
    if (lines.length === 0) return 0;
    const headerLooksLikeHeader = /nombre|name|cedula|id/i.test(lines[0]);
    return headerLooksLikeHeader ? Math.max(lines.length - 1, 0) : lines.length;
  };

  const onSubmitSeccional = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!nombre || !miembrosFile) {
      toast({
        title: "Campos requeridos",
        description: "Ingrese el nombre y adjunte el CSV de miembros.",
        variant: "default",
      });
      return;
    }
    try {
      setSubmitting(true);
      const slug = slugify(nombre);
      const ts = Date.now();

      // Leer y validar CSV
      const csvText = await readFileText(miembrosFile);
      const miembrosContados = contarMiembrosDesdeCSV(csvText);
      const miembrosMinOk = miembrosContados >= 15;

      // Subir CSV
      const csvPath = `seccionales/${slug}/miembros-${ts}.csv`;
      const { error: upCsvErr } = await supabase
        .storage
        .from("expedientes")
        .upload(csvPath, miembrosFile, { upsert: false, contentType: miembrosFile.type || "text/csv" });
      if (upCsvErr) throw upCsvErr;

      // Subir PDFs (si existen)
      let actasPaths: string[] = [];
      if (actasFiles.length > 0) {
        const uploads = actasFiles.map((file, i) => {
          const path = `seccionales/${slug}/acta-${i + 1}-${ts}.pdf`;
          return supabase.storage.from("expedientes").upload(path, file, { upsert: false, contentType: file.type || "application/pdf" })
            .then(({ error }) => {
              if (error) throw error;
              return path;
            });
        });
        actasPaths = await Promise.all(uploads);
      }

      // Guardar registro en BD
      const { error: insertErr } = await supabase.from("seccional_submissions").insert({
        seccional_nombre: nombre,
        directiva: directiva || null,
        miembros_csv_path: csvPath,
        actas_paths: actasPaths.length ? actasPaths : null,
        miembros_min_ok: miembrosMinOk,
        miembros_contados: miembrosContados,
        created_by: user?.id,
      });
      if (insertErr) throw insertErr;

      setUltimoResumen({ miembros: miembrosContados, ok: miembrosMinOk });
      setNombre("");
      setDirectiva("");
      setMiembrosFile(null);
      setActasFiles([]);

      toast({
        title: "Expediente enviado",
        description: miembrosMinOk
          ? "La seccional cumple con el mínimo de 15 miembros."
          : "Advertencia: la seccional no alcanza los 15 miembros.",
      });
    } catch (err: any) {
      toast({
        title: "Error al enviar",
        description: err?.message || "Intente nuevamente.",
        variant: "destructive",
      });
    } finally {
      setSubmitting(false);
    }
  };

  const onSubmitMember = async (e: React.FormEvent) => {
    e.preventDefault();
    console.log('Iniciando registro de miembro:', memberData);
    
    if (!memberData.nombreCompleto || !memberData.cedula || !memberData.organizacionId) {
      console.log('Faltan campos requeridos:', {
        nombreCompleto: !!memberData.nombreCompleto,
        cedula: !!memberData.cedula,
        organizacionId: !!memberData.organizacionId
      });
      toast({
        title: "Campos requeridos",
        description: "Complete al menos el nombre, cédula y organización.",
        variant: "destructive",
      });
      return;
    }

    try {
      setRegisteringMember(true);
      console.log('User context:', user);
      
      // Generate member number
      const org = organizaciones.find(o => o.id === memberData.organizacionId);
      console.log('Organización seleccionada:', org);
      const orgCode = org?.codigo || 'CLDCI';
      
      // Get next member number for this organization
      const { count, error: countError } = await supabase
        .from('miembros')
        .select('*', { count: 'exact', head: true })
        .eq('organizacion_id', memberData.organizacionId);
      
      if (countError) {
        console.error('Error contando miembros:', countError);
        throw countError;
      }
      
      console.log('Miembros existentes en organización:', count);
      const nextNumber = (count || 0) + 1;
      const numeroCarnet = `${orgCode}-${String(nextNumber).padStart(3, '0')}`;
      console.log('Número de carnet generado:', numeroCarnet);

      const memberInsert = {
        numero_carnet: numeroCarnet,
        nombre_completo: memberData.nombreCompleto,
        cedula: memberData.cedula,
        email: memberData.email || null,
        telefono: memberData.telefono || null,
        direccion: memberData.direccion || null,
        profesion: memberData.profesion || null,
        fecha_nacimiento: memberData.fechaNacimiento || null,
        organizacion_id: memberData.organizacionId,
        tipo_membresia: memberData.tipoMembresia as 'activo',
        estado_membresia: 'activa' as 'activa',
        fecha_ingreso: new Date().toISOString().split('T')[0],
        user_id: null
      };
      
      console.log('Datos a insertar:', memberInsert);

      const { data, error } = await supabase
        .from('miembros')
        .insert(memberInsert)
        .select();

      if (error) {
        console.error('Error insertando miembro:', error);
        throw error;
      }

      console.log('Miembro registrado exitosamente:', data);

      toast({
        title: "Miembro registrado",
        description: `Se ha registrado exitosamente con el carnet ${numeroCarnet}`,
      });

      // Reset form
      setMemberData({
        nombreCompleto: "",
        cedula: "",
        email: "",
        telefono: "",
        direccion: "",
        profesion: "",
        fechaNacimiento: "",
        organizacionId: "",
        tipoMembresia: "activo"
      });

      // Reload statistics
      loadStatistics();

    } catch (err: any) {
      console.error('Error completo al registrar miembro:', err);
      toast({
        title: "Error al registrar",
        description: err?.message || "Intente nuevamente.",
        variant: "destructive",
      });
    } finally {
      setRegisteringMember(false);
    }
  };

  const onSubmitGremio = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!gremioData.nombre || !gremioData.codigo || !gremioData.provincia) {
      toast({
        title: "Campos requeridos",
        description: "Complete al menos el nombre, código y provincia.",
        variant: "destructive",
      });
      return;
    }

    try {
      setRegisteringGremio(true);
      
      const { data, error } = await supabase
        .from('organizaciones')
        .insert({
          nombre: gremioData.nombre,
          codigo: gremioData.codigo,
          tipo: 'gremio',
          provincia: gremioData.provincia,
          ciudad: gremioData.ciudad,
          direccion: gremioData.direccion,
          telefono: gremioData.telefono,
          email: gremioData.email,
          estado_adecuacion: 'pendiente'
        })
        .select();

      if (error) throw error;

      toast({
        title: "Gremio registrado",
        description: `El gremio ${gremioData.nombre} ha sido registrado exitosamente.`,
      });

      // Reset form
      setGremioData({
        nombre: "",
        codigo: "",
        provincia: "",
        ciudad: "",
        direccion: "",
        telefono: "",
        email: "",
        presidente: "",
        secretario: "",
        tesorero: ""
      });

      loadOrganizaciones();
      loadStatistics();
    } catch (err: any) {
      toast({
        title: "Error al registrar gremio",
        description: err?.message || "Intente nuevamente.",
        variant: "destructive",
      });
    } finally {
      setRegisteringGremio(false);
    }
  };

  const onSubmitAsociacion = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!asociacionData.nombre || !asociacionData.codigo) {
      toast({
        title: "Campos requeridos",
        description: "Complete al menos el nombre y código.",
        variant: "destructive",
      });
      return;
    }

    try {
      setRegisteringAsociacion(true);
      
      const { data, error } = await supabase
        .from('organizaciones')
        .insert({
          nombre: asociacionData.nombre,
          codigo: asociacionData.codigo,
          tipo: 'asociacion',
          provincia: asociacionData.provincia,
          ciudad: asociacionData.ciudad,
          direccion: asociacionData.direccion,
          telefono: asociacionData.telefono,
          email: asociacionData.email,
          estado_adecuacion: 'pendiente'
        })
        .select();

      if (error) throw error;

      toast({
        title: "Asociación registrada",
        description: `La asociación ${asociacionData.nombre} ha sido registrada exitosamente.`,
      });

      // Reset form
      setAsociacionData({
        nombre: "",
        codigo: "",
        provincia: "",
        ciudad: "",
        direccion: "",
        telefono: "",
        email: "",
        tipoAsociacion: "profesional",
        presidente: "",
        secretario: ""
      });

      loadOrganizaciones();
      loadStatistics();
    } catch (err: any) {
      toast({
        title: "Error al registrar asociación",
        description: err?.message || "Intente nuevamente.",
        variant: "destructive",
      });
    } finally {
      setRegisteringAsociacion(false);
    }
  };

  const onSubmitEstudiante = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!estudianteData.nombreCompleto || !estudianteData.cedula || !estudianteData.universidad) {
      toast({
        title: "Campos requeridos",
        description: "Complete al menos el nombre, cédula y universidad.",
        variant: "destructive",
      });
      return;
    }

    try {
      setRegisteringEstudiante(true);
      
      // Find or create student organization
      let estudiantesOrg = organizaciones.find(o => o.nombre === "Estudiantes de Comunicación");
      
      if (!estudiantesOrg) {
        const { data: newOrg, error: orgError } = await supabase
          .from('organizaciones')
          .insert({
            nombre: "Estudiantes de Comunicación",
            codigo: "ESTCOM",
            tipo: 'asociacion',
            estado_adecuacion: 'aprobada'
          })
          .select()
          .single();

        if (orgError) throw orgError;
        estudiantesOrg = newOrg;
        await loadOrganizaciones();
      }

      // Generate student member number
      const { count } = await supabase
        .from('miembros')
        .select('*', { count: 'exact', head: true })
        .eq('organizacion_id', estudiantesOrg.id);
      
      const nextNumber = (count || 0) + 1;
      const numeroCarnet = `EST-${String(nextNumber).padStart(3, '0')}`;

      const { data, error } = await supabase
        .from('miembros')
        .insert({
          numero_carnet: numeroCarnet,
          nombre_completo: estudianteData.nombreCompleto,
          cedula: estudianteData.cedula,
          email: estudianteData.email || null,
          telefono: estudianteData.telefono || null,
          direccion: estudianteData.direccion || null,
          profesion: `Estudiante de ${estudianteData.carrera}`,
          fecha_nacimiento: estudianteData.fechaNacimiento || null,
          organizacion_id: estudiantesOrg.id,
          tipo_membresia: 'estudiante' as 'estudiante',
          estado_membresia: 'activa' as 'activa',
          fecha_ingreso: new Date().toISOString().split('T')[0],
          observaciones: `Universidad: ${estudianteData.universidad}, Carrera: ${estudianteData.carrera}, Semestre: ${estudianteData.semestre}, #Estudiante: ${estudianteData.numeroEstudiante}`,
          user_id: null
        })
        .select();

      if (error) throw error;

      toast({
        title: "Estudiante registrado",
        description: `Se ha registrado exitosamente con el carnet ${numeroCarnet}`,
      });

      // Reset form
      setEstudianteData({
        nombreCompleto: "",
        cedula: "",
        email: "",
        telefono: "",
        universidad: "",
        carrera: "",
        semestre: "",
        numeroEstudiante: "",
        fechaNacimiento: "",
        direccion: ""
      });

      loadStatistics();
    } catch (err: any) {
      toast({
        title: "Error al registrar estudiante",
        description: err?.message || "Intente nuevamente.",
        variant: "destructive",
      });
    } finally {
      setRegisteringEstudiante(false);
    }
  };

  return (
    <main className="min-h-screen bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700 text-white">
      <SEO
        title="Registro y Adecuación – CLDCI"
        description="Registro individual de miembros, estadísticas y adecuación de seccionales."
      />

      <div className="container mx-auto px-6 py-12">
        <div className="flex items-center gap-4 mb-6">
          <Link to="/" className="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-white/20 bg-white/10 hover:bg-white/20 text-white h-10 w-10 backdrop-blur-sm">
            <ArrowLeft className="h-4 w-4" />
          </Link>
          <h1 className="text-3xl font-bold text-white">Registro y Adecuación</h1>
        </div>
        
        <Tabs defaultValue="miembro" className="space-y-6">
          <TabsList className="grid w-full grid-cols-6 bg-white/10 backdrop-blur-sm border-white/20">
            <TabsTrigger value="miembro" className="text-white data-[state=active]:bg-white/20">
              <UserPlus className="w-4 h-4 mr-2" />
              Miembro
            </TabsTrigger>
            <TabsTrigger value="gremios" className="text-white data-[state=active]:bg-white/20">
              <Briefcase className="w-4 h-4 mr-2" />
              Gremios
            </TabsTrigger>
            <TabsTrigger value="asociaciones" className="text-white data-[state=active]:bg-white/20">
              <School className="w-4 h-4 mr-2" />
              Asociaciones
            </TabsTrigger>
            <TabsTrigger value="estudiantes" className="text-white data-[state=active]:bg-white/20">
              <GraduationCap className="w-4 h-4 mr-2" />
              Estudiantes
            </TabsTrigger>
            <TabsTrigger value="seccional" className="text-white data-[state=active]:bg-white/20">
              <Building className="w-4 h-4 mr-2" />
              Seccionales
            </TabsTrigger>
            <TabsTrigger value="estadisticas" className="text-white data-[state=active]:bg-white/20">
              <BarChart3 className="w-4 h-4 mr-2" />
              Estadísticas
            </TabsTrigger>
          </TabsList>

          <TabsContent value="miembro" className="space-y-6">
            <div className="grid lg:grid-cols-2 gap-6">
              <Card className="bg-white/10 backdrop-blur-sm border-white/20 text-white">
                <CardHeader>
                  <CardTitle className="text-white flex items-center gap-2">
                    <UserPlus className="w-5 h-5" />
                    Registro de Nuevo Miembro
                  </CardTitle>
                </CardHeader>
                <form onSubmit={onSubmitMember}>
                  <CardContent className="space-y-4">
                    <div className="grid grid-cols-2 gap-4">
                      <div>
                        <Label htmlFor="nombreCompleto" className="text-white">Nombre Completo</Label>
                        <Input 
                          id="nombreCompleto" 
                          placeholder="Nombre y apellidos"
                          value={memberData.nombreCompleto}
                          onChange={(e) => setMemberData(prev => ({ ...prev, nombreCompleto: e.target.value }))}
                          className="bg-white/10 border-white/20 text-white placeholder:text-white/60" 
                          required
                        />
                      </div>
                      <div>
                        <Label htmlFor="cedula" className="text-white">Cédula</Label>
                        <Input 
                          id="cedula" 
                          placeholder="000-0000000-0"
                          value={memberData.cedula}
                          onChange={(e) => setMemberData(prev => ({ ...prev, cedula: e.target.value }))}
                          className="bg-white/10 border-white/20 text-white placeholder:text-white/60"
                          required
                        />
                      </div>
                    </div>
                    
                    <div className="grid grid-cols-2 gap-4">
                      <div>
                        <Label htmlFor="email" className="text-white">Email</Label>
                        <Input 
                          id="email" 
                          type="email"
                          placeholder="correo@ejemplo.com"
                          value={memberData.email}
                          onChange={(e) => setMemberData(prev => ({ ...prev, email: e.target.value }))}
                          className="bg-white/10 border-white/20 text-white placeholder:text-white/60"
                        />
                      </div>
                      <div>
                        <Label htmlFor="telefono" className="text-white">Teléfono</Label>
                        <Input 
                          id="telefono" 
                          placeholder="809-000-0000"
                          value={memberData.telefono}
                          onChange={(e) => setMemberData(prev => ({ ...prev, telefono: e.target.value }))}
                          className="bg-white/10 border-white/20 text-white placeholder:text-white/60"
                        />
                      </div>
                    </div>

                    <div>
                      <Label htmlFor="organizacion" className="text-white">Organización</Label>
                      <Select 
                        value={memberData.organizacionId}
                        onValueChange={(value) => setMemberData(prev => ({ ...prev, organizacionId: value }))}
                        required
                      >
                        <SelectTrigger className="bg-white/10 border-white/20 text-white">
                          <SelectValue placeholder="Seleccionar organización" />
                        </SelectTrigger>
                        <SelectContent>
                          {organizaciones.map((org) => (
                            <SelectItem key={org.id} value={org.id}>
                              {org.nombre}
                            </SelectItem>
                          ))}
                        </SelectContent>
                      </Select>
                    </div>

                    <div className="grid grid-cols-2 gap-4">
                      <div>
                        <Label htmlFor="profesion" className="text-white">Profesión</Label>
                        <Input 
                          id="profesion" 
                          placeholder="Locutor, Productor, etc."
                          value={memberData.profesion}
                          onChange={(e) => setMemberData(prev => ({ ...prev, profesion: e.target.value }))}
                          className="bg-white/10 border-white/20 text-white placeholder:text-white/60"
                        />
                      </div>
                      <div>
                        <Label htmlFor="fechaNacimiento" className="text-white">Fecha de Nacimiento</Label>
                        <Input 
                          id="fechaNacimiento" 
                          type="date"
                          value={memberData.fechaNacimiento}
                          onChange={(e) => setMemberData(prev => ({ ...prev, fechaNacimiento: e.target.value }))}
                          className="bg-white/10 border-white/20 text-white"
                        />
                      </div>
                    </div>

                    <div>
                      <Label htmlFor="direccion" className="text-white">Dirección</Label>
                      <Textarea 
                        id="direccion" 
                        placeholder="Dirección completa"
                        value={memberData.direccion}
                        onChange={(e) => setMemberData(prev => ({ ...prev, direccion: e.target.value }))}
                        className="bg-white/10 border-white/20 text-white placeholder:text-white/60"
                      />
                    </div>
                  </CardContent>
                  <CardFooter>
                    <Button 
                      type="submit" 
                      disabled={registeringMember} 
                      className="w-full bg-yellow-400 text-blue-900 hover:bg-yellow-500 font-semibold"
                    >
                      {registeringMember ? "Registrando..." : "Registrar Miembro"}
                    </Button>
                  </CardFooter>
                </form>
              </Card>

              <Card className="bg-white/10 backdrop-blur-sm border-white/20 text-white">
                <CardHeader>
                  <CardTitle className="text-white">Información de Registro</CardTitle>
                </CardHeader>
                <CardContent className="space-y-4">
                  <div className="space-y-3">
                    <div className="flex items-center justify-between p-3 bg-white/5 rounded-lg">
                      <span className="text-blue-200">Miembros Totales</span>
                      <span className="text-2xl font-bold text-white">{stats.totalMiembros}</span>
                    </div>
                    <div className="flex items-center justify-between p-3 bg-white/5 rounded-lg">
                      <span className="text-blue-200">Miembros Activos</span>
                      <span className="text-2xl font-bold text-green-400">{stats.miembrosActivos}</span>
                    </div>
                    <div className="flex items-center justify-between p-3 bg-white/5 rounded-lg">
                      <span className="text-blue-200">Nuevos Este Mes</span>
                      <span className="text-2xl font-bold text-yellow-400">{stats.nuevosEsteMes}</span>
                    </div>
                    <div className="flex items-center justify-between p-3 bg-white/5 rounded-lg">
                      <span className="text-blue-200">Organizaciones</span>
                      <span className="text-2xl font-bold text-white">{stats.organizacionesTotal}</span>
                    </div>
                  </div>
                  
                  <div className="pt-4 border-t border-white/20">
                    <h4 className="text-white font-semibold mb-3">Requisitos de Registro</h4>
                    <div className="space-y-2 text-sm text-blue-200">
                      <p>• Nombre completo y cédula (obligatorio)</p>
                      <p>• Seleccionar organización de afiliación</p>
                      <p>• Información de contacto recomendada</p>
                      <p>• Se generará carnet automáticamente</p>
                    </div>
                  </div>
                </CardContent>
              </Card>
            </div>
          </TabsContent>

          <TabsContent value="gremios" className="space-y-6">
            <div className="mb-4">
              <h2 className="text-2xl font-bold text-white mb-2">Registro de Gremios</h2>
              <p className="text-blue-200">Registre un nuevo gremio profesional o sindical del sector comunicación.</p>
            </div>
            
            <Card className="bg-white/10 backdrop-blur-sm border-white/20 text-white">
              <CardHeader>
                <CardTitle className="text-white flex items-center gap-2">
                  <Briefcase className="w-5 h-5" />
                  Datos del Gremio
                </CardTitle>
              </CardHeader>
              <form onSubmit={onSubmitGremio}>
                <CardContent className="space-y-4">
                  <div className="grid grid-cols-2 gap-4">
                    <div>
                      <Label htmlFor="gremio-nombre" className="text-white">Nombre del Gremio</Label>
                      <Input 
                        id="gremio-nombre" 
                        placeholder="Ej: Gremio de Locutores de Santiago"
                        value={gremioData.nombre}
                        onChange={(e) => setGremioData(prev => ({ ...prev, nombre: e.target.value }))}
                        className="bg-white/10 border-white/20 text-white placeholder:text-white/60" 
                        required
                      />
                    </div>
                    <div>
                      <Label htmlFor="gremio-codigo" className="text-white">Código</Label>
                      <Input 
                        id="gremio-codigo" 
                        placeholder="Ej: GLSGO"
                        value={gremioData.codigo}
                        onChange={(e) => setGremioData(prev => ({ ...prev, codigo: e.target.value }))}
                        className="bg-white/10 border-white/20 text-white placeholder:text-white/60"
                        required
                      />
                    </div>
                  </div>
                  
                  <div className="grid grid-cols-2 gap-4">
                    <div>
                      <Label htmlFor="gremio-provincia" className="text-white">Provincia</Label>
                      <Input 
                        id="gremio-provincia" 
                        placeholder="Ej: Santiago"
                        value={gremioData.provincia}
                        onChange={(e) => setGremioData(prev => ({ ...prev, provincia: e.target.value }))}
                        className="bg-white/10 border-white/20 text-white placeholder:text-white/60"
                        required
                      />
                    </div>
                    <div>
                      <Label htmlFor="gremio-ciudad" className="text-white">Ciudad</Label>
                      <Input 
                        id="gremio-ciudad" 
                        placeholder="Ej: Santiago"
                        value={gremioData.ciudad}
                        onChange={(e) => setGremioData(prev => ({ ...prev, ciudad: e.target.value }))}
                        className="bg-white/10 border-white/20 text-white placeholder:text-white/60"
                      />
                    </div>
                  </div>

                  <div className="grid grid-cols-2 gap-4">
                    <div>
                      <Label htmlFor="gremio-email" className="text-white">Email</Label>
                      <Input 
                        id="gremio-email" 
                        type="email"
                        placeholder="contacto@gremio.org"
                        value={gremioData.email}
                        onChange={(e) => setGremioData(prev => ({ ...prev, email: e.target.value }))}
                        className="bg-white/10 border-white/20 text-white placeholder:text-white/60"
                      />
                    </div>
                    <div>
                      <Label htmlFor="gremio-telefono" className="text-white">Teléfono</Label>
                      <Input 
                        id="gremio-telefono" 
                        placeholder="809-000-0000"
                        value={gremioData.telefono}
                        onChange={(e) => setGremioData(prev => ({ ...prev, telefono: e.target.value }))}
                        className="bg-white/10 border-white/20 text-white placeholder:text-white/60"
                      />
                    </div>
                  </div>

                  <div>
                    <Label htmlFor="gremio-direccion" className="text-white">Dirección</Label>
                    <Textarea 
                      id="gremio-direccion" 
                      placeholder="Dirección completa del gremio"
                      value={gremioData.direccion}
                      onChange={(e) => setGremioData(prev => ({ ...prev, direccion: e.target.value }))}
                      className="bg-white/10 border-white/20 text-white placeholder:text-white/60"
                    />
                  </div>

                  <div className="grid grid-cols-3 gap-4">
                    <div>
                      <Label htmlFor="gremio-presidente" className="text-white">Presidente</Label>
                      <Input 
                        id="gremio-presidente" 
                        placeholder="Nombre del presidente"
                        value={gremioData.presidente}
                        onChange={(e) => setGremioData(prev => ({ ...prev, presidente: e.target.value }))}
                        className="bg-white/10 border-white/20 text-white placeholder:text-white/60"
                      />
                    </div>
                    <div>
                      <Label htmlFor="gremio-secretario" className="text-white">Secretario</Label>
                      <Input 
                        id="gremio-secretario" 
                        placeholder="Nombre del secretario"
                        value={gremioData.secretario}
                        onChange={(e) => setGremioData(prev => ({ ...prev, secretario: e.target.value }))}
                        className="bg-white/10 border-white/20 text-white placeholder:text-white/60"
                      />
                    </div>
                    <div>
                      <Label htmlFor="gremio-tesorero" className="text-white">Tesorero</Label>
                      <Input 
                        id="gremio-tesorero" 
                        placeholder="Nombre del tesorero"
                        value={gremioData.tesorero}
                        onChange={(e) => setGremioData(prev => ({ ...prev, tesorero: e.target.value }))}
                        className="bg-white/10 border-white/20 text-white placeholder:text-white/60"
                      />
                    </div>
                  </div>
                </CardContent>
                <CardFooter>
                  <Button 
                    type="submit" 
                    disabled={registeringGremio} 
                    className="w-full bg-yellow-400 text-blue-900 hover:bg-yellow-500 font-semibold"
                  >
                    {registeringGremio ? "Registrando..." : "Registrar Gremio"}
                  </Button>
                </CardFooter>
              </form>
            </Card>
          </TabsContent>

          <TabsContent value="asociaciones" className="space-y-6">
            <div className="mb-4">
              <h2 className="text-2xl font-bold text-white mb-2">Registro de Asociaciones Afiliadas</h2>
              <p className="text-blue-200">Registre una asociación profesional o cultural afiliada al sector comunicación.</p>
            </div>
            
            <Card className="bg-white/10 backdrop-blur-sm border-white/20 text-white">
              <CardHeader>
                <CardTitle className="text-white flex items-center gap-2">
                  <School className="w-5 h-5" />
                  Datos de la Asociación
                </CardTitle>
              </CardHeader>
              <form onSubmit={onSubmitAsociacion}>
                <CardContent className="space-y-4">
                  <div className="grid grid-cols-2 gap-4">
                    <div>
                      <Label htmlFor="asociacion-nombre" className="text-white">Nombre de la Asociación</Label>
                      <Input 
                        id="asociacion-nombre" 
                        placeholder="Ej: Asociación de Periodistas Deportivos"
                        value={asociacionData.nombre}
                        onChange={(e) => setAsociacionData(prev => ({ ...prev, nombre: e.target.value }))}
                        className="bg-white/10 border-white/20 text-white placeholder:text-white/60" 
                        required
                      />
                    </div>
                    <div>
                      <Label htmlFor="asociacion-codigo" className="text-white">Código</Label>
                      <Input 
                        id="asociacion-codigo" 
                        placeholder="Ej: APEDEP"
                        value={asociacionData.codigo}
                        onChange={(e) => setAsociacionData(prev => ({ ...prev, codigo: e.target.value }))}
                        className="bg-white/10 border-white/20 text-white placeholder:text-white/60"
                        required
                      />
                    </div>
                  </div>

                  <div>
                    <Label htmlFor="asociacion-tipo" className="text-white">Tipo de Asociación</Label>
                    <Select 
                      value={asociacionData.tipoAsociacion}
                      onValueChange={(value) => setAsociacionData(prev => ({ ...prev, tipoAsociacion: value }))}
                    >
                      <SelectTrigger className="bg-white/10 border-white/20 text-white">
                        <SelectValue placeholder="Seleccionar tipo" />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectItem value="profesional">Profesional</SelectItem>
                        <SelectItem value="cultural">Cultural</SelectItem>
                        <SelectItem value="educativa">Educativa</SelectItem>
                        <SelectItem value="deportiva">Deportiva</SelectItem>
                        <SelectItem value="social">Social</SelectItem>
                      </SelectContent>
                    </Select>
                  </div>
                  
                  <div className="grid grid-cols-2 gap-4">
                    <div>
                      <Label htmlFor="asociacion-provincia" className="text-white">Provincia</Label>
                      <Input 
                        id="asociacion-provincia" 
                        placeholder="Ej: Santo Domingo"
                        value={asociacionData.provincia}
                        onChange={(e) => setAsociacionData(prev => ({ ...prev, provincia: e.target.value }))}
                        className="bg-white/10 border-white/20 text-white placeholder:text-white/60"
                      />
                    </div>
                    <div>
                      <Label htmlFor="asociacion-ciudad" className="text-white">Ciudad</Label>
                      <Input 
                        id="asociacion-ciudad" 
                        placeholder="Ej: Santo Domingo"
                        value={asociacionData.ciudad}
                        onChange={(e) => setAsociacionData(prev => ({ ...prev, ciudad: e.target.value }))}
                        className="bg-white/10 border-white/20 text-white placeholder:text-white/60"
                      />
                    </div>
                  </div>

                  <div className="grid grid-cols-2 gap-4">
                    <div>
                      <Label htmlFor="asociacion-email" className="text-white">Email</Label>
                      <Input 
                        id="asociacion-email" 
                        type="email"
                        placeholder="info@asociacion.org"
                        value={asociacionData.email}
                        onChange={(e) => setAsociacionData(prev => ({ ...prev, email: e.target.value }))}
                        className="bg-white/10 border-white/20 text-white placeholder:text-white/60"
                      />
                    </div>
                    <div>
                      <Label htmlFor="asociacion-telefono" className="text-white">Teléfono</Label>
                      <Input 
                        id="asociacion-telefono" 
                        placeholder="809-000-0000"
                        value={asociacionData.telefono}
                        onChange={(e) => setAsociacionData(prev => ({ ...prev, telefono: e.target.value }))}
                        className="bg-white/10 border-white/20 text-white placeholder:text-white/60"
                      />
                    </div>
                  </div>

                  <div>
                    <Label htmlFor="asociacion-direccion" className="text-white">Dirección</Label>
                    <Textarea 
                      id="asociacion-direccion" 
                      placeholder="Dirección completa de la asociación"
                      value={asociacionData.direccion}
                      onChange={(e) => setAsociacionData(prev => ({ ...prev, direccion: e.target.value }))}
                      className="bg-white/10 border-white/20 text-white placeholder:text-white/60"
                    />
                  </div>

                  <div className="grid grid-cols-2 gap-4">
                    <div>
                      <Label htmlFor="asociacion-presidente" className="text-white">Presidente</Label>
                      <Input 
                        id="asociacion-presidente" 
                        placeholder="Nombre del presidente"
                        value={asociacionData.presidente}
                        onChange={(e) => setAsociacionData(prev => ({ ...prev, presidente: e.target.value }))}
                        className="bg-white/10 border-white/20 text-white placeholder:text-white/60"
                      />
                    </div>
                    <div>
                      <Label htmlFor="asociacion-secretario" className="text-white">Secretario</Label>
                      <Input 
                        id="asociacion-secretario" 
                        placeholder="Nombre del secretario"
                        value={asociacionData.secretario}
                        onChange={(e) => setAsociacionData(prev => ({ ...prev, secretario: e.target.value }))}
                        className="bg-white/10 border-white/20 text-white placeholder:text-white/60"
                      />
                    </div>
                  </div>
                </CardContent>
                <CardFooter>
                  <Button 
                    type="submit" 
                    disabled={registeringAsociacion} 
                    className="w-full bg-yellow-400 text-blue-900 hover:bg-yellow-500 font-semibold"
                  >
                    {registeringAsociacion ? "Registrando..." : "Registrar Asociación"}
                  </Button>
                </CardFooter>
              </form>
            </Card>
          </TabsContent>

          <TabsContent value="estudiantes" className="space-y-6">
            <div className="mb-4">
              <h2 className="text-2xl font-bold text-white mb-2">Registro de Estudiantes</h2>
              <p className="text-blue-200">Registro especial para estudiantes de comunicación, periodismo y carreras afines.</p>
            </div>
            
            <div className="grid lg:grid-cols-2 gap-6">
              <Card className="bg-white/10 backdrop-blur-sm border-white/20 text-white">
                <CardHeader>
                  <CardTitle className="text-white flex items-center gap-2">
                    <GraduationCap className="w-5 h-5" />
                    Datos del Estudiante
                  </CardTitle>
                </CardHeader>
                <form onSubmit={onSubmitEstudiante}>
                  <CardContent className="space-y-4">
                    <div className="grid grid-cols-2 gap-4">
                      <div>
                        <Label htmlFor="estudiante-nombre" className="text-white">Nombre Completo</Label>
                        <Input 
                          id="estudiante-nombre" 
                          placeholder="Nombre y apellidos"
                          value={estudianteData.nombreCompleto}
                          onChange={(e) => setEstudianteData(prev => ({ ...prev, nombreCompleto: e.target.value }))}
                          className="bg-white/10 border-white/20 text-white placeholder:text-white/60" 
                          required
                        />
                      </div>
                      <div>
                        <Label htmlFor="estudiante-cedula" className="text-white">Cédula</Label>
                        <Input 
                          id="estudiante-cedula" 
                          placeholder="000-0000000-0"
                          value={estudianteData.cedula}
                          onChange={(e) => setEstudianteData(prev => ({ ...prev, cedula: e.target.value }))}
                          className="bg-white/10 border-white/20 text-white placeholder:text-white/60"
                          required
                        />
                      </div>
                    </div>
                    
                    <div className="grid grid-cols-2 gap-4">
                      <div>
                        <Label htmlFor="estudiante-email" className="text-white">Email</Label>
                        <Input 
                          id="estudiante-email" 
                          type="email"
                          placeholder="estudiante@universidad.edu.do"
                          value={estudianteData.email}
                          onChange={(e) => setEstudianteData(prev => ({ ...prev, email: e.target.value }))}
                          className="bg-white/10 border-white/20 text-white placeholder:text-white/60"
                        />
                      </div>
                      <div>
                        <Label htmlFor="estudiante-telefono" className="text-white">Teléfono</Label>
                        <Input 
                          id="estudiante-telefono" 
                          placeholder="809-000-0000"
                          value={estudianteData.telefono}
                          onChange={(e) => setEstudianteData(prev => ({ ...prev, telefono: e.target.value }))}
                          className="bg-white/10 border-white/20 text-white placeholder:text-white/60"
                        />
                      </div>
                    </div>

                    <div>
                      <Label htmlFor="estudiante-universidad" className="text-white">Universidad</Label>
                      <Input 
                        id="estudiante-universidad" 
                        placeholder="Ej: Universidad Autónoma de Santo Domingo"
                        value={estudianteData.universidad}
                        onChange={(e) => setEstudianteData(prev => ({ ...prev, universidad: e.target.value }))}
                        className="bg-white/10 border-white/20 text-white placeholder:text-white/60"
                        required
                      />
                    </div>

                    <div className="grid grid-cols-2 gap-4">
                      <div>
                        <Label htmlFor="estudiante-carrera" className="text-white">Carrera</Label>
                        <Input 
                          id="estudiante-carrera" 
                          placeholder="Ej: Comunicación Social"
                          value={estudianteData.carrera}
                          onChange={(e) => setEstudianteData(prev => ({ ...prev, carrera: e.target.value }))}
                          className="bg-white/10 border-white/20 text-white placeholder:text-white/60"
                        />
                      </div>
                      <div>
                        <Label htmlFor="estudiante-semestre" className="text-white">Semestre</Label>
                        <Select 
                          value={estudianteData.semestre}
                          onValueChange={(value) => setEstudianteData(prev => ({ ...prev, semestre: value }))}
                        >
                          <SelectTrigger className="bg-white/10 border-white/20 text-white">
                            <SelectValue placeholder="Seleccionar" />
                          </SelectTrigger>
                          <SelectContent>
                            {[1,2,3,4,5,6,7,8,9,10].map(sem => (
                              <SelectItem key={sem} value={sem.toString()}>{sem}° Semestre</SelectItem>
                            ))}
                          </SelectContent>
                        </Select>
                      </div>
                    </div>

                    <div className="grid grid-cols-2 gap-4">
                      <div>
                        <Label htmlFor="estudiante-numero" className="text-white">Número de Estudiante</Label>
                        <Input 
                          id="estudiante-numero" 
                          placeholder="Matrícula universitaria"
                          value={estudianteData.numeroEstudiante}
                          onChange={(e) => setEstudianteData(prev => ({ ...prev, numeroEstudiante: e.target.value }))}
                          className="bg-white/10 border-white/20 text-white placeholder:text-white/60"
                        />
                      </div>
                      <div>
                        <Label htmlFor="estudiante-nacimiento" className="text-white">Fecha de Nacimiento</Label>
                        <Input 
                          id="estudiante-nacimiento" 
                          type="date"
                          value={estudianteData.fechaNacimiento}
                          onChange={(e) => setEstudianteData(prev => ({ ...prev, fechaNacimiento: e.target.value }))}
                          className="bg-white/10 border-white/20 text-white"
                        />
                      </div>
                    </div>

                    <div>
                      <Label htmlFor="estudiante-direccion" className="text-white">Dirección</Label>
                      <Textarea 
                        id="estudiante-direccion" 
                        placeholder="Dirección de residencia"
                        value={estudianteData.direccion}
                        onChange={(e) => setEstudianteData(prev => ({ ...prev, direccion: e.target.value }))}
                        className="bg-white/10 border-white/20 text-white placeholder:text-white/60"
                      />
                    </div>
                  </CardContent>
                  <CardFooter>
                    <Button 
                      type="submit" 
                      disabled={registeringEstudiante} 
                      className="w-full bg-yellow-400 text-blue-900 hover:bg-yellow-500 font-semibold"
                    >
                      {registeringEstudiante ? "Registrando..." : "Registrar Estudiante"}
                    </Button>
                  </CardFooter>
                </form>
              </Card>

              <Card className="bg-white/10 backdrop-blur-sm border-white/20 text-white">
                <CardHeader>
                  <CardTitle className="text-white">Beneficios Estudiantiles</CardTitle>
                </CardHeader>
                <CardContent className="space-y-4">
                  <div className="space-y-3">
                    <div className="p-3 bg-white/5 rounded-lg">
                      <h4 className="font-semibold text-white mb-2">Membresía Gratuita</h4>
                      <p className="text-sm text-blue-200">Los estudiantes acceden sin costo durante sus estudios.</p>
                    </div>
                    <div className="p-3 bg-white/5 rounded-lg">
                      <h4 className="font-semibold text-white mb-2">Capacitaciones</h4>
                      <p className="text-sm text-blue-200">Acceso preferencial a talleres y seminarios.</p>
                    </div>
                    <div className="p-3 bg-white/5 rounded-lg">
                      <h4 className="font-semibold text-white mb-2">Prácticas Profesionales</h4>
                      <p className="text-sm text-blue-200">Oportunidades de pasantías en medios afiliados.</p>
                    </div>
                    <div className="p-3 bg-white/5 rounded-lg">
                      <h4 className="font-semibold text-white mb-2">Networking</h4>
                      <p className="text-sm text-blue-200">Conecta con profesionales del sector.</p>
                    </div>
                  </div>
                  
                  <div className="pt-4 border-t border-white/20">
                    <h4 className="text-white font-semibold mb-3">Requisitos</h4>
                    <div className="space-y-2 text-sm text-blue-200">
                      <p>• Estar inscrito en carrera afín a comunicación</p>
                      <p>• Presentar certificación de estudios</p>
                      <p>• Completar formulario de registro</p>
                      <p>• Renovar cada año académico</p>
                    </div>
                  </div>
                </CardContent>
              </Card>
            </div>
          </TabsContent>

          <TabsContent value="estadisticas" className="space-y-6">
            {loadingStats ? (
              <Card className="bg-white/10 backdrop-blur-sm border-white/20 text-white">
                <CardContent className="flex items-center justify-center py-12">
                  <div className="text-center">
                    <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-white mx-auto mb-4"></div>
                    <p className="text-white">Cargando estadísticas...</p>
                  </div>
                </CardContent>
              </Card>
            ) : (
              <>
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                  <Card className="bg-white/10 backdrop-blur-sm border-white/20 text-white">
                    <CardContent className="p-6 text-center">
                      <Users className="w-8 h-8 mx-auto mb-2 text-blue-300" />
                      <div className="text-3xl font-bold text-white">{stats.totalMiembros}</div>
                      <p className="text-blue-200">Total Miembros</p>
                    </CardContent>
                  </Card>
                  
                  <Card className="bg-white/10 backdrop-blur-sm border-white/20 text-white">
                    <CardContent className="p-6 text-center">
                      <TrendingUp className="w-8 h-8 mx-auto mb-2 text-green-400" />
                      <div className="text-3xl font-bold text-green-400">{stats.miembrosActivos}</div>
                      <p className="text-blue-200">Miembros Activos</p>
                    </CardContent>
                  </Card>
                  
                  <Card className="bg-white/10 backdrop-blur-sm border-white/20 text-white">
                    <CardContent className="p-6 text-center">
                      <Calendar className="w-8 h-8 mx-auto mb-2 text-yellow-400" />
                      <div className="text-3xl font-bold text-yellow-400">{stats.nuevosEsteMes}</div>
                      <p className="text-blue-200">Nuevos Este Mes</p>
                    </CardContent>
                  </Card>
                  
                  <Card className="bg-white/10 backdrop-blur-sm border-white/20 text-white">
                    <CardContent className="p-6 text-center">
                      <Building className="w-8 h-8 mx-auto mb-2 text-purple-400" />
                      <div className="text-3xl font-bold text-purple-400">{stats.organizacionesTotal}</div>
                      <p className="text-blue-200">Organizaciones</p>
                    </CardContent>
                  </Card>
                </div>

                <Card className="bg-white/10 backdrop-blur-sm border-white/20 text-white">
                  <CardHeader>
                    <CardTitle className="text-white flex items-center gap-2">
                      <MapPin className="w-5 h-5" />
                      Distribución por Provincias
                    </CardTitle>
                  </CardHeader>
                  <CardContent>
                    <div className="space-y-4">
                      {stats.distribucionProvincias.slice(0, 8).map((item, index) => (
                        <div key={item.provincia} className="flex items-center justify-between">
                          <div className="flex items-center gap-3">
                            <div className="w-2 h-2 rounded-full bg-blue-400"></div>
                            <span className="text-white">{item.provincia}</span>
                          </div>
                          <div className="flex items-center gap-3">
                            <Progress 
                              value={(item.count / stats.totalMiembros) * 100} 
                              className="w-24 h-2" 
                            />
                            <span className="text-blue-200 min-w-[2rem] text-right">{item.count}</span>
                          </div>
                        </div>
                      ))}
                    </div>
                  </CardContent>
                </Card>
              </>
            )}
          </TabsContent>

          <TabsContent value="seccional" className="space-y-6">
            <div className="mb-4">
              <h2 className="text-2xl font-bold text-white mb-2">Registro de Seccionales</h2>
              <p className="text-blue-200">Complete el expediente de su seccional. El sistema verificará automáticamente requisitos como el mínimo de 15 miembros.</p>
            </div>
            
            <form className="grid lg:grid-cols-2 gap-6" onSubmit={onSubmitSeccional}>
              <Card className="bg-white/10 backdrop-blur-sm border-white/20 text-white">
                <CardHeader>
                  <CardTitle className="text-white">Datos de la Seccional</CardTitle>
                </CardHeader>
                <CardContent className="space-y-4">
                  <div>
                    <Label htmlFor="nombre" className="text-white">Nombre de la Seccional</Label>
                    <Input 
                      id="nombre" 
                      placeholder="Ej: San Cristóbal" 
                      value={nombre} 
                      onChange={(e) => setNombre(e.target.value)} 
                      className="bg-white/10 border-white/20 text-white placeholder:text-white/60" 
                    />
                  </div>
                  <div>
                    <Label htmlFor="directiva" className="text-white">Directiva Actual</Label>
                    <Textarea 
                      id="directiva" 
                      placeholder="Presidente, Secretario, Tesorero, Vocales…" 
                      value={directiva} 
                      onChange={(e) => setDirectiva(e.target.value)} 
                      className="bg-white/10 border-white/20 text-white placeholder:text-white/60" 
                    />
                  </div>
                  <div>
                    <Label htmlFor="miembros" className="text-white">Lista de Miembros (CSV)</Label>
                    <Input 
                      id="miembros" 
                      type="file" 
                      accept=".csv,text/csv" 
                      onChange={(e) => setMiembrosFile(e.target.files?.[0] || null)} 
                      className="bg-white/10 border-white/20 text-white file:bg-yellow-400 file:text-blue-900 file:border-0 file:rounded file:px-3 file:py-1 file:mr-3" 
                    />
                  </div>
                  <div>
                    <Label htmlFor="actas" className="text-white">Actas y Reglamentos (PDF)</Label>
                    <Input 
                      id="actas" 
                      type="file" 
                      multiple 
                      accept="application/pdf" 
                      onChange={(e) => setActasFiles(Array.from(e.target.files || []))} 
                      className="bg-white/10 border-white/20 text-white file:bg-yellow-400 file:text-blue-900 file:border-0 file:rounded file:px-3 file:py-1 file:mr-3" 
                    />
                  </div>
                </CardContent>
                <CardFooter>
                  <Button 
                    type="submit" 
                    disabled={submitting} 
                    className="bg-yellow-400 text-blue-900 hover:bg-yellow-500 font-semibold"
                  >
                    {submitting ? "Enviando…" : "Enviar expediente"}
                  </Button>
                </CardFooter>
              </Card>

              <Card className="bg-white/10 backdrop-blur-sm border-white/20 text-white">
                <CardHeader>
                  <CardTitle className="text-white">Validaciones automáticas</CardTitle>
                </CardHeader>
                <CardContent className="space-y-2 text-sm text-blue-200">
                  <p>• Verificación de al menos 15 miembros con datos mínimos.</p>
                  <p>• Validación de formatos y presencia de actas y reglamentos.</p>
                  <p>• Generación de reporte de observaciones para corrección.</p>
                  {ultimoResumen && (
                    <div className="mt-4 text-white">
                      <p><strong>Miembros contados:</strong> {ultimoResumen.miembros}</p>
                      <p><strong>Resultado:</strong> {ultimoResumen.ok ? "Cumple con el mínimo" : "No cumple (menos de 15)"}</p>
                    </div>
                  )}
                </CardContent>
              </Card>
            </form>
          </TabsContent>
        </Tabs>
      </div>
    </main>
  );
};

export default RegistroAdecuacion;

