export type Json =
  | string
  | number
  | boolean
  | null
  | { [key: string]: Json | undefined }
  | Json[]

export type Database = {
  // Allows to automatically instantiate createClient with right options
  // instead of createClient<Database, { PostgrestVersion: 'XX' }>(URL, KEY)
  __InternalSupabase: {
    PostgrestVersion: "13.0.4"
  }
  public: {
    Tables: {
      asambleas: {
        Row: {
          acta_url: string | null
          asistentes_count: number | null
          convocatoria_url: string | null
          created_at: string | null
          created_by: string | null
          descripcion: string | null
          enlace_virtual: string | null
          estado: string | null
          fecha_asamblea: string
          fecha_convocatoria: string
          id: string
          lugar: string | null
          modalidad: string | null
          organizacion_id: string
          quorum_alcanzado: boolean | null
          quorum_minimo: number
          tipo: string
          titulo: string
          updated_at: string | null
        }
        Insert: {
          acta_url?: string | null
          asistentes_count?: number | null
          convocatoria_url?: string | null
          created_at?: string | null
          created_by?: string | null
          descripcion?: string | null
          enlace_virtual?: string | null
          estado?: string | null
          fecha_asamblea: string
          fecha_convocatoria: string
          id?: string
          lugar?: string | null
          modalidad?: string | null
          organizacion_id: string
          quorum_alcanzado?: boolean | null
          quorum_minimo: number
          tipo: string
          titulo: string
          updated_at?: string | null
        }
        Update: {
          acta_url?: string | null
          asistentes_count?: number | null
          convocatoria_url?: string | null
          created_at?: string | null
          created_by?: string | null
          descripcion?: string | null
          enlace_virtual?: string | null
          estado?: string | null
          fecha_asamblea?: string
          fecha_convocatoria?: string
          id?: string
          lugar?: string | null
          modalidad?: string | null
          organizacion_id?: string
          quorum_alcanzado?: boolean | null
          quorum_minimo?: number
          tipo?: string
          titulo?: string
          updated_at?: string | null
        }
        Relationships: [
          {
            foreignKeyName: "asambleas_organizacion_id_fkey"
            columns: ["organizacion_id"]
            isOneToOne: false
            referencedRelation: "organizaciones"
            referencedColumns: ["id"]
          },
        ]
      }
      asambleas_generales: {
        Row: {
          acta_url: string | null
          asistentes_count: number | null
          created_at: string | null
          created_by: string | null
          enlace_virtual: string | null
          estado: string | null
          fecha_celebracion: string
          fecha_convocatoria: string
          id: string
          lugar: string | null
          modalidad: string | null
          orden_dia: string[] | null
          organizacion_id: string | null
          quorum_alcanzado: boolean | null
          quorum_minimo: number
          tema_principal: string
          tipo_asamblea: string
          updated_at: string | null
        }
        Insert: {
          acta_url?: string | null
          asistentes_count?: number | null
          created_at?: string | null
          created_by?: string | null
          enlace_virtual?: string | null
          estado?: string | null
          fecha_celebracion: string
          fecha_convocatoria: string
          id?: string
          lugar?: string | null
          modalidad?: string | null
          orden_dia?: string[] | null
          organizacion_id?: string | null
          quorum_alcanzado?: boolean | null
          quorum_minimo: number
          tema_principal: string
          tipo_asamblea: string
          updated_at?: string | null
        }
        Update: {
          acta_url?: string | null
          asistentes_count?: number | null
          created_at?: string | null
          created_by?: string | null
          enlace_virtual?: string | null
          estado?: string | null
          fecha_celebracion?: string
          fecha_convocatoria?: string
          id?: string
          lugar?: string | null
          modalidad?: string | null
          orden_dia?: string[] | null
          organizacion_id?: string | null
          quorum_alcanzado?: boolean | null
          quorum_minimo?: number
          tema_principal?: string
          tipo_asamblea?: string
          updated_at?: string | null
        }
        Relationships: [
          {
            foreignKeyName: "asambleas_generales_organizacion_id_fkey"
            columns: ["organizacion_id"]
            isOneToOne: false
            referencedRelation: "organizaciones"
            referencedColumns: ["id"]
          },
        ]
      }
      asistencia_asambleas: {
        Row: {
          asamblea_id: string
          hora_registro: string | null
          id: string
          miembro_id: string
          modalidad: string | null
          presente: boolean
        }
        Insert: {
          asamblea_id: string
          hora_registro?: string | null
          id?: string
          miembro_id: string
          modalidad?: string | null
          presente: boolean
        }
        Update: {
          asamblea_id?: string
          hora_registro?: string | null
          id?: string
          miembro_id?: string
          modalidad?: string | null
          presente?: boolean
        }
        Relationships: [
          {
            foreignKeyName: "asistencia_asambleas_asamblea_id_fkey"
            columns: ["asamblea_id"]
            isOneToOne: false
            referencedRelation: "asambleas"
            referencedColumns: ["id"]
          },
          {
            foreignKeyName: "asistencia_asambleas_miembro_id_fkey"
            columns: ["miembro_id"]
            isOneToOne: false
            referencedRelation: "miembros"
            referencedColumns: ["id"]
          },
        ]
      }
      capacitaciones: {
        Row: {
          capacidad_maxima: number | null
          certificado_template_url: string | null
          costo: number | null
          created_at: string | null
          created_by: string | null
          descripcion: string | null
          enlace_virtual: string | null
          estado: string | null
          fecha_fin: string | null
          fecha_inicio: string
          id: string
          lugar: string | null
          modalidad: string | null
          organizacion_id: string | null
          tipo: string
          titulo: string
        }
        Insert: {
          capacidad_maxima?: number | null
          certificado_template_url?: string | null
          costo?: number | null
          created_at?: string | null
          created_by?: string | null
          descripcion?: string | null
          enlace_virtual?: string | null
          estado?: string | null
          fecha_fin?: string | null
          fecha_inicio: string
          id?: string
          lugar?: string | null
          modalidad?: string | null
          organizacion_id?: string | null
          tipo: string
          titulo: string
        }
        Update: {
          capacidad_maxima?: number | null
          certificado_template_url?: string | null
          costo?: number | null
          created_at?: string | null
          created_by?: string | null
          descripcion?: string | null
          enlace_virtual?: string | null
          estado?: string | null
          fecha_fin?: string | null
          fecha_inicio?: string
          id?: string
          lugar?: string | null
          modalidad?: string | null
          organizacion_id?: string | null
          tipo?: string
          titulo?: string
        }
        Relationships: [
          {
            foreignKeyName: "capacitaciones_organizacion_id_fkey"
            columns: ["organizacion_id"]
            isOneToOne: false
            referencedRelation: "organizaciones"
            referencedColumns: ["id"]
          },
        ]
      }
      cargos_organos: {
        Row: {
          activo: boolean | null
          created_at: string | null
          descripcion: string | null
          id: string
          nivel_autoridad: number | null
          nombre_cargo: string
          organo_id: string
        }
        Insert: {
          activo?: boolean | null
          created_at?: string | null
          descripcion?: string | null
          id?: string
          nivel_autoridad?: number | null
          nombre_cargo: string
          organo_id: string
        }
        Update: {
          activo?: boolean | null
          created_at?: string | null
          descripcion?: string | null
          id?: string
          nivel_autoridad?: number | null
          nombre_cargo?: string
          organo_id?: string
        }
        Relationships: [
          {
            foreignKeyName: "cargos_organos_organo_id_fkey"
            columns: ["organo_id"]
            isOneToOne: false
            referencedRelation: "organos_cldc"
            referencedColumns: ["id"]
          },
        ]
      }
      comites_ejecutivos_seccionales: {
        Row: {
          activo: boolean | null
          cargo: string
          created_at: string | null
          fecha_fin: string | null
          fecha_inicio: string
          id: string
          miembro_id: string
          periodo: string
          seccional_id: string
        }
        Insert: {
          activo?: boolean | null
          cargo: string
          created_at?: string | null
          fecha_fin?: string | null
          fecha_inicio: string
          id?: string
          miembro_id: string
          periodo: string
          seccional_id: string
        }
        Update: {
          activo?: boolean | null
          cargo?: string
          created_at?: string | null
          fecha_fin?: string | null
          fecha_inicio?: string
          id?: string
          miembro_id?: string
          periodo?: string
          seccional_id?: string
        }
        Relationships: [
          {
            foreignKeyName: "comites_ejecutivos_seccionales_miembro_id_fkey"
            columns: ["miembro_id"]
            isOneToOne: false
            referencedRelation: "miembros"
            referencedColumns: ["id"]
          },
          {
            foreignKeyName: "comites_ejecutivos_seccionales_seccional_id_fkey"
            columns: ["seccional_id"]
            isOneToOne: false
            referencedRelation: "seccionales"
            referencedColumns: ["id"]
          },
        ]
      }
      cursos: {
        Row: {
          capacidad_maxima: number | null
          categoria: string
          certificado_template_url: string | null
          created_at: string
          created_by: string | null
          descripcion: string | null
          duracion_horas: number
          enlace_virtual: string | null
          estado: string
          fecha_fin: string
          fecha_inicio: string
          id: string
          imagen_url: string | null
          inscritos_count: number | null
          instructor: string | null
          lugar: string | null
          modalidad: string
          nivel: string
          objetivos: string[] | null
          organizacion_id: string | null
          precio: number | null
          requisitos: string[] | null
          temario: Json | null
          titulo: string
          updated_at: string
        }
        Insert: {
          capacidad_maxima?: number | null
          categoria?: string
          certificado_template_url?: string | null
          created_at?: string
          created_by?: string | null
          descripcion?: string | null
          duracion_horas?: number
          enlace_virtual?: string | null
          estado?: string
          fecha_fin: string
          fecha_inicio: string
          id?: string
          imagen_url?: string | null
          inscritos_count?: number | null
          instructor?: string | null
          lugar?: string | null
          modalidad?: string
          nivel?: string
          objetivos?: string[] | null
          organizacion_id?: string | null
          precio?: number | null
          requisitos?: string[] | null
          temario?: Json | null
          titulo: string
          updated_at?: string
        }
        Update: {
          capacidad_maxima?: number | null
          categoria?: string
          certificado_template_url?: string | null
          created_at?: string
          created_by?: string | null
          descripcion?: string | null
          duracion_horas?: number
          enlace_virtual?: string | null
          estado?: string
          fecha_fin?: string
          fecha_inicio?: string
          id?: string
          imagen_url?: string | null
          inscritos_count?: number | null
          instructor?: string | null
          lugar?: string | null
          modalidad?: string
          nivel?: string
          objetivos?: string[] | null
          organizacion_id?: string | null
          precio?: number | null
          requisitos?: string[] | null
          temario?: Json | null
          titulo?: string
          updated_at?: string
        }
        Relationships: [
          {
            foreignKeyName: "cursos_organizacion_id_fkey"
            columns: ["organizacion_id"]
            isOneToOne: false
            referencedRelation: "organizaciones"
            referencedColumns: ["id"]
          },
        ]
      }
      customers: {
        Row: {
          address: string | null
          company_id: string
          created_at: string | null
          email: string | null
          id: string
          lat: number | null
          lng: number | null
          name: string
          phone: string | null
          updated_at: string | null
        }
        Insert: {
          address?: string | null
          company_id: string
          created_at?: string | null
          email?: string | null
          id?: string
          lat?: number | null
          lng?: number | null
          name: string
          phone?: string | null
          updated_at?: string | null
        }
        Update: {
          address?: string | null
          company_id?: string
          created_at?: string | null
          email?: string | null
          id?: string
          lat?: number | null
          lng?: number | null
          name?: string
          phone?: string | null
          updated_at?: string | null
        }
        Relationships: [
          {
            foreignKeyName: "customers_company_id_fkey"
            columns: ["company_id"]
            isOneToOne: false
            referencedRelation: "delivery_companies"
            referencedColumns: ["id"]
          },
        ]
      }
      data_access_audit: {
        Row: {
          accessed_at: string | null
          action: string
          id: string
          ip_address: unknown | null
          organization_context: string | null
          record_id: string | null
          sensitive_fields: string[] | null
          table_name: string
          user_agent: string | null
          user_id: string | null
          user_role: string | null
        }
        Insert: {
          accessed_at?: string | null
          action: string
          id?: string
          ip_address?: unknown | null
          organization_context?: string | null
          record_id?: string | null
          sensitive_fields?: string[] | null
          table_name: string
          user_agent?: string | null
          user_id?: string | null
          user_role?: string | null
        }
        Update: {
          accessed_at?: string | null
          action?: string
          id?: string
          ip_address?: unknown | null
          organization_context?: string | null
          record_id?: string | null
          sensitive_fields?: string[] | null
          table_name?: string
          user_agent?: string | null
          user_id?: string | null
          user_role?: string | null
        }
        Relationships: []
      }
      delegados_asamblea: {
        Row: {
          asamblea_id: string
          fecha_registro: string | null
          id: string
          miembro_id: string | null
          observaciones: string | null
          organizacion_origen_id: string | null
          presente: boolean | null
          tipo_delegado: string
        }
        Insert: {
          asamblea_id: string
          fecha_registro?: string | null
          id?: string
          miembro_id?: string | null
          observaciones?: string | null
          organizacion_origen_id?: string | null
          presente?: boolean | null
          tipo_delegado: string
        }
        Update: {
          asamblea_id?: string
          fecha_registro?: string | null
          id?: string
          miembro_id?: string | null
          observaciones?: string | null
          organizacion_origen_id?: string | null
          presente?: boolean | null
          tipo_delegado?: string
        }
        Relationships: [
          {
            foreignKeyName: "delegados_asamblea_asamblea_id_fkey"
            columns: ["asamblea_id"]
            isOneToOne: false
            referencedRelation: "asambleas_generales"
            referencedColumns: ["id"]
          },
          {
            foreignKeyName: "delegados_asamblea_miembro_id_fkey"
            columns: ["miembro_id"]
            isOneToOne: false
            referencedRelation: "miembros"
            referencedColumns: ["id"]
          },
          {
            foreignKeyName: "delegados_asamblea_organizacion_origen_id_fkey"
            columns: ["organizacion_origen_id"]
            isOneToOne: false
            referencedRelation: "organizaciones"
            referencedColumns: ["id"]
          },
        ]
      }
      delivery_companies: {
        Row: {
          address: string | null
          created_at: string | null
          email: string | null
          id: string
          logo_url: string | null
          name: string
          phone: string | null
          primary_color: string | null
          secondary_color: string | null
          updated_at: string | null
        }
        Insert: {
          address?: string | null
          created_at?: string | null
          email?: string | null
          id?: string
          logo_url?: string | null
          name: string
          phone?: string | null
          primary_color?: string | null
          secondary_color?: string | null
          updated_at?: string | null
        }
        Update: {
          address?: string | null
          created_at?: string | null
          email?: string | null
          id?: string
          logo_url?: string | null
          name?: string
          phone?: string | null
          primary_color?: string | null
          secondary_color?: string | null
          updated_at?: string | null
        }
        Relationships: []
      }
      delivery_feedback: {
        Row: {
          comment: string | null
          created_at: string | null
          id: string
          order_id: string
          rating: number | null
        }
        Insert: {
          comment?: string | null
          created_at?: string | null
          id?: string
          order_id: string
          rating?: number | null
        }
        Update: {
          comment?: string | null
          created_at?: string | null
          id?: string
          order_id?: string
          rating?: number | null
        }
        Relationships: [
          {
            foreignKeyName: "delivery_feedback_order_id_fkey"
            columns: ["order_id"]
            isOneToOne: false
            referencedRelation: "delivery_orders"
            referencedColumns: ["id"]
          },
        ]
      }
      delivery_orders: {
        Row: {
          company_id: string
          created_at: string | null
          customer_id: string
          delivery_address: string
          delivery_instructions: string | null
          delivery_lat: number | null
          delivery_lng: number | null
          id: string
          notes: string | null
          order_number: string
          pickup_address: string | null
          pickup_lat: number | null
          pickup_lng: number | null
          pin_code: string | null
          preferred_time_end: string | null
          preferred_time_start: string | null
          priority: string | null
          requires_pin: boolean | null
          requires_signature: boolean | null
          status: string | null
          tracking_token: string | null
          updated_at: string | null
          volume: number | null
          weight: number | null
        }
        Insert: {
          company_id: string
          created_at?: string | null
          customer_id: string
          delivery_address: string
          delivery_instructions?: string | null
          delivery_lat?: number | null
          delivery_lng?: number | null
          id?: string
          notes?: string | null
          order_number: string
          pickup_address?: string | null
          pickup_lat?: number | null
          pickup_lng?: number | null
          pin_code?: string | null
          preferred_time_end?: string | null
          preferred_time_start?: string | null
          priority?: string | null
          requires_pin?: boolean | null
          requires_signature?: boolean | null
          status?: string | null
          tracking_token?: string | null
          updated_at?: string | null
          volume?: number | null
          weight?: number | null
        }
        Update: {
          company_id?: string
          created_at?: string | null
          customer_id?: string
          delivery_address?: string
          delivery_instructions?: string | null
          delivery_lat?: number | null
          delivery_lng?: number | null
          id?: string
          notes?: string | null
          order_number?: string
          pickup_address?: string | null
          pickup_lat?: number | null
          pickup_lng?: number | null
          pin_code?: string | null
          preferred_time_end?: string | null
          preferred_time_start?: string | null
          priority?: string | null
          requires_pin?: boolean | null
          requires_signature?: boolean | null
          status?: string | null
          tracking_token?: string | null
          updated_at?: string | null
          volume?: number | null
          weight?: number | null
        }
        Relationships: [
          {
            foreignKeyName: "delivery_orders_company_id_fkey"
            columns: ["company_id"]
            isOneToOne: false
            referencedRelation: "delivery_companies"
            referencedColumns: ["id"]
          },
          {
            foreignKeyName: "delivery_orders_customer_id_fkey"
            columns: ["customer_id"]
            isOneToOne: false
            referencedRelation: "customers"
            referencedColumns: ["id"]
          },
        ]
      }
      delivery_routes: {
        Row: {
          actual_end_time: string | null
          actual_start_time: string | null
          company_id: string
          created_at: string | null
          driver_id: string | null
          id: string
          optimization_data: Json | null
          planned_end_time: string | null
          planned_start_time: string | null
          route_name: string
          status: string | null
          total_distance: number | null
          total_duration_minutes: number | null
          updated_at: string | null
          vehicle_id: string | null
        }
        Insert: {
          actual_end_time?: string | null
          actual_start_time?: string | null
          company_id: string
          created_at?: string | null
          driver_id?: string | null
          id?: string
          optimization_data?: Json | null
          planned_end_time?: string | null
          planned_start_time?: string | null
          route_name: string
          status?: string | null
          total_distance?: number | null
          total_duration_minutes?: number | null
          updated_at?: string | null
          vehicle_id?: string | null
        }
        Update: {
          actual_end_time?: string | null
          actual_start_time?: string | null
          company_id?: string
          created_at?: string | null
          driver_id?: string | null
          id?: string
          optimization_data?: Json | null
          planned_end_time?: string | null
          planned_start_time?: string | null
          route_name?: string
          status?: string | null
          total_distance?: number | null
          total_duration_minutes?: number | null
          updated_at?: string | null
          vehicle_id?: string | null
        }
        Relationships: [
          {
            foreignKeyName: "delivery_routes_company_id_fkey"
            columns: ["company_id"]
            isOneToOne: false
            referencedRelation: "delivery_companies"
            referencedColumns: ["id"]
          },
          {
            foreignKeyName: "delivery_routes_driver_id_fkey"
            columns: ["driver_id"]
            isOneToOne: false
            referencedRelation: "drivers"
            referencedColumns: ["id"]
          },
          {
            foreignKeyName: "delivery_routes_vehicle_id_fkey"
            columns: ["vehicle_id"]
            isOneToOne: false
            referencedRelation: "vehicles"
            referencedColumns: ["id"]
          },
        ]
      }
      diplomados: {
        Row: {
          capacidad_maxima: number | null
          categoria: string
          certificado_template_url: string | null
          coordinador_academico: string | null
          created_at: string
          created_by: string | null
          creditos_academicos: number | null
          descripcion: string | null
          duracion_meses: number
          enlace_virtual: string | null
          estado: string
          fecha_fin: string
          fecha_inicio: string
          id: string
          imagen_url: string | null
          inscritos_count: number | null
          lugar: string | null
          modalidad: string
          organizacion_id: string | null
          perfil_egreso: string[] | null
          plan_estudios: Json | null
          precio: number | null
          requisitos_ingreso: string[] | null
          titulo: string
          updated_at: string
        }
        Insert: {
          capacidad_maxima?: number | null
          categoria?: string
          certificado_template_url?: string | null
          coordinador_academico?: string | null
          created_at?: string
          created_by?: string | null
          creditos_academicos?: number | null
          descripcion?: string | null
          duracion_meses?: number
          enlace_virtual?: string | null
          estado?: string
          fecha_fin: string
          fecha_inicio: string
          id?: string
          imagen_url?: string | null
          inscritos_count?: number | null
          lugar?: string | null
          modalidad?: string
          organizacion_id?: string | null
          perfil_egreso?: string[] | null
          plan_estudios?: Json | null
          precio?: number | null
          requisitos_ingreso?: string[] | null
          titulo: string
          updated_at?: string
        }
        Update: {
          capacidad_maxima?: number | null
          categoria?: string
          certificado_template_url?: string | null
          coordinador_academico?: string | null
          created_at?: string
          created_by?: string | null
          creditos_academicos?: number | null
          descripcion?: string | null
          duracion_meses?: number
          enlace_virtual?: string | null
          estado?: string
          fecha_fin?: string
          fecha_inicio?: string
          id?: string
          imagen_url?: string | null
          inscritos_count?: number | null
          lugar?: string | null
          modalidad?: string
          organizacion_id?: string | null
          perfil_egreso?: string[] | null
          plan_estudios?: Json | null
          precio?: number | null
          requisitos_ingreso?: string[] | null
          titulo?: string
          updated_at?: string
        }
        Relationships: [
          {
            foreignKeyName: "diplomados_organizacion_id_fkey"
            columns: ["organizacion_id"]
            isOneToOne: false
            referencedRelation: "organizaciones"
            referencedColumns: ["id"]
          },
        ]
      }
      driver_messages: {
        Row: {
          company_id: string
          created_at: string | null
          driver_id: string
          id: string
          is_read: boolean | null
          message: string
          sender_id: string
        }
        Insert: {
          company_id: string
          created_at?: string | null
          driver_id: string
          id?: string
          is_read?: boolean | null
          message: string
          sender_id: string
        }
        Update: {
          company_id?: string
          created_at?: string | null
          driver_id?: string
          id?: string
          is_read?: boolean | null
          message?: string
          sender_id?: string
        }
        Relationships: [
          {
            foreignKeyName: "driver_messages_company_id_fkey"
            columns: ["company_id"]
            isOneToOne: false
            referencedRelation: "delivery_companies"
            referencedColumns: ["id"]
          },
          {
            foreignKeyName: "driver_messages_driver_id_fkey"
            columns: ["driver_id"]
            isOneToOne: false
            referencedRelation: "drivers"
            referencedColumns: ["id"]
          },
        ]
      }
      drivers: {
        Row: {
          company_id: string
          created_at: string | null
          current_lat: number | null
          current_lng: number | null
          email: string
          id: string
          is_active: boolean | null
          last_location_update: string | null
          license_number: string | null
          name: string
          phone: string | null
          photo_url: string | null
          updated_at: string | null
          user_id: string | null
        }
        Insert: {
          company_id: string
          created_at?: string | null
          current_lat?: number | null
          current_lng?: number | null
          email: string
          id?: string
          is_active?: boolean | null
          last_location_update?: string | null
          license_number?: string | null
          name: string
          phone?: string | null
          photo_url?: string | null
          updated_at?: string | null
          user_id?: string | null
        }
        Update: {
          company_id?: string
          created_at?: string | null
          current_lat?: number | null
          current_lng?: number | null
          email?: string
          id?: string
          is_active?: boolean | null
          last_location_update?: string | null
          license_number?: string | null
          name?: string
          phone?: string | null
          photo_url?: string | null
          updated_at?: string | null
          user_id?: string | null
        }
        Relationships: [
          {
            foreignKeyName: "drivers_company_id_fkey"
            columns: ["company_id"]
            isOneToOne: false
            referencedRelation: "delivery_companies"
            referencedColumns: ["id"]
          },
        ]
      }
      elecciones: {
        Row: {
          auditoria_hash: string | null
          candidatos: Json
          cargo: string
          created_at: string | null
          created_by: string | null
          estado: string | null
          fecha_fin: string
          fecha_inicio: string
          id: string
          modalidad: string | null
          padron_id: string
          resultados: Json | null
          votos_totales: number | null
        }
        Insert: {
          auditoria_hash?: string | null
          candidatos: Json
          cargo: string
          created_at?: string | null
          created_by?: string | null
          estado?: string | null
          fecha_fin: string
          fecha_inicio: string
          id?: string
          modalidad?: string | null
          padron_id: string
          resultados?: Json | null
          votos_totales?: number | null
        }
        Update: {
          auditoria_hash?: string | null
          candidatos?: Json
          cargo?: string
          created_at?: string | null
          created_by?: string | null
          estado?: string | null
          fecha_fin?: string
          fecha_inicio?: string
          id?: string
          modalidad?: string | null
          padron_id?: string
          resultados?: Json | null
          votos_totales?: number | null
        }
        Relationships: [
          {
            foreignKeyName: "elecciones_padron_id_fkey"
            columns: ["padron_id"]
            isOneToOne: false
            referencedRelation: "padrones_electorales"
            referencedColumns: ["id"]
          },
        ]
      }
      electores: {
        Row: {
          elegible: boolean | null
          id: string
          miembro_id: string
          observaciones: string | null
          padron_id: string
        }
        Insert: {
          elegible?: boolean | null
          id?: string
          miembro_id: string
          observaciones?: string | null
          padron_id: string
        }
        Update: {
          elegible?: boolean | null
          id?: string
          miembro_id?: string
          observaciones?: string | null
          padron_id?: string
        }
        Relationships: [
          {
            foreignKeyName: "electores_miembro_id_fkey"
            columns: ["miembro_id"]
            isOneToOne: false
            referencedRelation: "miembros"
            referencedColumns: ["id"]
          },
          {
            foreignKeyName: "electores_padron_id_fkey"
            columns: ["padron_id"]
            isOneToOne: false
            referencedRelation: "padrones_electorales"
            referencedColumns: ["id"]
          },
        ]
      }
      evaluaciones_modulos: {
        Row: {
          calificacion: number
          created_at: string
          evaluador: string | null
          fecha_evaluacion: string
          id: string
          inscripcion_diplomado_id: string
          modulo_id: string
          observaciones: string | null
        }
        Insert: {
          calificacion: number
          created_at?: string
          evaluador?: string | null
          fecha_evaluacion?: string
          id?: string
          inscripcion_diplomado_id: string
          modulo_id: string
          observaciones?: string | null
        }
        Update: {
          calificacion?: number
          created_at?: string
          evaluador?: string | null
          fecha_evaluacion?: string
          id?: string
          inscripcion_diplomado_id?: string
          modulo_id?: string
          observaciones?: string | null
        }
        Relationships: [
          {
            foreignKeyName: "evaluaciones_modulos_inscripcion_diplomado_id_fkey"
            columns: ["inscripcion_diplomado_id"]
            isOneToOne: false
            referencedRelation: "inscripciones_diplomados"
            referencedColumns: ["id"]
          },
          {
            foreignKeyName: "evaluaciones_modulos_modulo_id_fkey"
            columns: ["modulo_id"]
            isOneToOne: false
            referencedRelation: "modulos_diplomados"
            referencedColumns: ["id"]
          },
        ]
      }
      inscripciones_capacitacion: {
        Row: {
          asistio: boolean | null
          calificacion: number | null
          capacitacion_id: string
          certificado_url: string | null
          fecha_inscripcion: string | null
          id: string
          miembro_id: string
          observaciones: string | null
        }
        Insert: {
          asistio?: boolean | null
          calificacion?: number | null
          capacitacion_id: string
          certificado_url?: string | null
          fecha_inscripcion?: string | null
          id?: string
          miembro_id: string
          observaciones?: string | null
        }
        Update: {
          asistio?: boolean | null
          calificacion?: number | null
          capacitacion_id?: string
          certificado_url?: string | null
          fecha_inscripcion?: string | null
          id?: string
          miembro_id?: string
          observaciones?: string | null
        }
        Relationships: [
          {
            foreignKeyName: "inscripciones_capacitacion_capacitacion_id_fkey"
            columns: ["capacitacion_id"]
            isOneToOne: false
            referencedRelation: "capacitaciones"
            referencedColumns: ["id"]
          },
          {
            foreignKeyName: "inscripciones_capacitacion_miembro_id_fkey"
            columns: ["miembro_id"]
            isOneToOne: false
            referencedRelation: "miembros"
            referencedColumns: ["id"]
          },
        ]
      }
      inscripciones_cursos: {
        Row: {
          asistencia_porcentaje: number | null
          calificacion_final: number | null
          certificado_obtenido: boolean | null
          certificado_url: string | null
          comprobante_pago_url: string | null
          created_at: string
          curso_id: string
          estado_inscripcion: string
          fecha_inscripcion: string
          fecha_pago: string | null
          id: string
          metodo_pago: string | null
          miembro_id: string
          monto_pagado: number | null
          observaciones: string | null
          updated_at: string
        }
        Insert: {
          asistencia_porcentaje?: number | null
          calificacion_final?: number | null
          certificado_obtenido?: boolean | null
          certificado_url?: string | null
          comprobante_pago_url?: string | null
          created_at?: string
          curso_id: string
          estado_inscripcion?: string
          fecha_inscripcion?: string
          fecha_pago?: string | null
          id?: string
          metodo_pago?: string | null
          miembro_id: string
          monto_pagado?: number | null
          observaciones?: string | null
          updated_at?: string
        }
        Update: {
          asistencia_porcentaje?: number | null
          calificacion_final?: number | null
          certificado_obtenido?: boolean | null
          certificado_url?: string | null
          comprobante_pago_url?: string | null
          created_at?: string
          curso_id?: string
          estado_inscripcion?: string
          fecha_inscripcion?: string
          fecha_pago?: string | null
          id?: string
          metodo_pago?: string | null
          miembro_id?: string
          monto_pagado?: number | null
          observaciones?: string | null
          updated_at?: string
        }
        Relationships: [
          {
            foreignKeyName: "inscripciones_cursos_curso_id_fkey"
            columns: ["curso_id"]
            isOneToOne: false
            referencedRelation: "cursos"
            referencedColumns: ["id"]
          },
          {
            foreignKeyName: "inscripciones_cursos_miembro_id_fkey"
            columns: ["miembro_id"]
            isOneToOne: false
            referencedRelation: "miembros"
            referencedColumns: ["id"]
          },
        ]
      }
      inscripciones_diplomados: {
        Row: {
          comprobante_pago_url: string | null
          created_at: string
          creditos_obtenidos: number | null
          diploma_obtenido: boolean | null
          diploma_url: string | null
          diplomado_id: string
          estado_inscripcion: string
          fecha_inscripcion: string
          fecha_pago: string | null
          id: string
          metodo_pago: string | null
          miembro_id: string
          monto_pagado: number | null
          observaciones: string | null
          promedio_general: number | null
          updated_at: string
        }
        Insert: {
          comprobante_pago_url?: string | null
          created_at?: string
          creditos_obtenidos?: number | null
          diploma_obtenido?: boolean | null
          diploma_url?: string | null
          diplomado_id: string
          estado_inscripcion?: string
          fecha_inscripcion?: string
          fecha_pago?: string | null
          id?: string
          metodo_pago?: string | null
          miembro_id: string
          monto_pagado?: number | null
          observaciones?: string | null
          promedio_general?: number | null
          updated_at?: string
        }
        Update: {
          comprobante_pago_url?: string | null
          created_at?: string
          creditos_obtenidos?: number | null
          diploma_obtenido?: boolean | null
          diploma_url?: string | null
          diplomado_id?: string
          estado_inscripcion?: string
          fecha_inscripcion?: string
          fecha_pago?: string | null
          id?: string
          metodo_pago?: string | null
          miembro_id?: string
          monto_pagado?: number | null
          observaciones?: string | null
          promedio_general?: number | null
          updated_at?: string
        }
        Relationships: [
          {
            foreignKeyName: "inscripciones_diplomados_diplomado_id_fkey"
            columns: ["diplomado_id"]
            isOneToOne: false
            referencedRelation: "diplomados"
            referencedColumns: ["id"]
          },
          {
            foreignKeyName: "inscripciones_diplomados_miembro_id_fkey"
            columns: ["miembro_id"]
            isOneToOne: false
            referencedRelation: "miembros"
            referencedColumns: ["id"]
          },
        ]
      }
      member_access_log: {
        Row: {
          access_timestamp: string | null
          access_type: string
          accessed_member_id: string
          accessing_user_id: string
          id: string
          organization_context: string | null
          user_role: string
        }
        Insert: {
          access_timestamp?: string | null
          access_type: string
          accessed_member_id: string
          accessing_user_id: string
          id?: string
          organization_context?: string | null
          user_role: string
        }
        Update: {
          access_timestamp?: string | null
          access_type?: string
          accessed_member_id?: string
          accessing_user_id?: string
          id?: string
          organization_context?: string | null
          user_role?: string
        }
        Relationships: []
      }
      miembros: {
        Row: {
          cedula: string | null
          created_at: string | null
          direccion: string | null
          email: string | null
          estado_membresia:
            | Database["public"]["Enums"]["estado_membresia"]
            | null
          fecha_fundacion: string | null
          fecha_ingreso: string | null
          fecha_nacimiento: string | null
          fecha_suspension: string | null
          fecha_vencimiento: string | null
          foto_url: string | null
          id: string
          institucion_educativa: string | null
          motivo_suspension: string | null
          nombre_completo: string
          numero_carnet: string
          observaciones: string | null
          organizacion_id: string
          pais_residencia: string | null
          profesion: string | null
          reconocimiento_detalle: string | null
          telefono: string | null
          tipo_membresia: Database["public"]["Enums"]["tipo_membresia"] | null
          updated_at: string | null
          user_id: string | null
        }
        Insert: {
          cedula?: string | null
          created_at?: string | null
          direccion?: string | null
          email?: string | null
          estado_membresia?:
            | Database["public"]["Enums"]["estado_membresia"]
            | null
          fecha_fundacion?: string | null
          fecha_ingreso?: string | null
          fecha_nacimiento?: string | null
          fecha_suspension?: string | null
          fecha_vencimiento?: string | null
          foto_url?: string | null
          id?: string
          institucion_educativa?: string | null
          motivo_suspension?: string | null
          nombre_completo: string
          numero_carnet: string
          observaciones?: string | null
          organizacion_id: string
          pais_residencia?: string | null
          profesion?: string | null
          reconocimiento_detalle?: string | null
          telefono?: string | null
          tipo_membresia?: Database["public"]["Enums"]["tipo_membresia"] | null
          updated_at?: string | null
          user_id?: string | null
        }
        Update: {
          cedula?: string | null
          created_at?: string | null
          direccion?: string | null
          email?: string | null
          estado_membresia?:
            | Database["public"]["Enums"]["estado_membresia"]
            | null
          fecha_fundacion?: string | null
          fecha_ingreso?: string | null
          fecha_nacimiento?: string | null
          fecha_suspension?: string | null
          fecha_vencimiento?: string | null
          foto_url?: string | null
          id?: string
          institucion_educativa?: string | null
          motivo_suspension?: string | null
          nombre_completo?: string
          numero_carnet?: string
          observaciones?: string | null
          organizacion_id?: string
          pais_residencia?: string | null
          profesion?: string | null
          reconocimiento_detalle?: string | null
          telefono?: string | null
          tipo_membresia?: Database["public"]["Enums"]["tipo_membresia"] | null
          updated_at?: string | null
          user_id?: string | null
        }
        Relationships: [
          {
            foreignKeyName: "miembros_organizacion_id_fkey"
            columns: ["organizacion_id"]
            isOneToOne: false
            referencedRelation: "organizaciones"
            referencedColumns: ["id"]
          },
        ]
      }
      miembros_directivos: {
        Row: {
          cargo_id: string
          created_at: string | null
          email_institucional: string | null
          es_presidente: boolean | null
          estado: string | null
          fecha_fin: string | null
          fecha_inicio: string
          foto_url: string | null
          id: string
          miembro_id: string | null
          organo_id: string
          periodo: string
          semblanza: string | null
          telefono_institucional: string | null
          updated_at: string | null
        }
        Insert: {
          cargo_id: string
          created_at?: string | null
          email_institucional?: string | null
          es_presidente?: boolean | null
          estado?: string | null
          fecha_fin?: string | null
          fecha_inicio: string
          foto_url?: string | null
          id?: string
          miembro_id?: string | null
          organo_id: string
          periodo: string
          semblanza?: string | null
          telefono_institucional?: string | null
          updated_at?: string | null
        }
        Update: {
          cargo_id?: string
          created_at?: string | null
          email_institucional?: string | null
          es_presidente?: boolean | null
          estado?: string | null
          fecha_fin?: string | null
          fecha_inicio?: string
          foto_url?: string | null
          id?: string
          miembro_id?: string | null
          organo_id?: string
          periodo?: string
          semblanza?: string | null
          telefono_institucional?: string | null
          updated_at?: string | null
        }
        Relationships: [
          {
            foreignKeyName: "miembros_directivos_cargo_id_fkey"
            columns: ["cargo_id"]
            isOneToOne: false
            referencedRelation: "cargos_organos"
            referencedColumns: ["id"]
          },
          {
            foreignKeyName: "miembros_directivos_miembro_id_fkey"
            columns: ["miembro_id"]
            isOneToOne: false
            referencedRelation: "miembros"
            referencedColumns: ["id"]
          },
          {
            foreignKeyName: "miembros_directivos_organo_id_fkey"
            columns: ["organo_id"]
            isOneToOne: false
            referencedRelation: "organos_cldc"
            referencedColumns: ["id"]
          },
        ]
      }
      modulos_diplomados: {
        Row: {
          contenido: Json | null
          created_at: string
          descripcion: string | null
          diplomado_id: string
          duracion_horas: number
          evaluacion_tipo: string | null
          fecha_fin: string | null
          fecha_inicio: string | null
          id: string
          instructor: string | null
          nombre_modulo: string
          orden: number
          peso_evaluacion: number | null
          recursos_url: string[] | null
          updated_at: string
        }
        Insert: {
          contenido?: Json | null
          created_at?: string
          descripcion?: string | null
          diplomado_id: string
          duracion_horas?: number
          evaluacion_tipo?: string | null
          fecha_fin?: string | null
          fecha_inicio?: string | null
          id?: string
          instructor?: string | null
          nombre_modulo: string
          orden: number
          peso_evaluacion?: number | null
          recursos_url?: string[] | null
          updated_at?: string
        }
        Update: {
          contenido?: Json | null
          created_at?: string
          descripcion?: string | null
          diplomado_id?: string
          duracion_horas?: number
          evaluacion_tipo?: string | null
          fecha_fin?: string | null
          fecha_inicio?: string | null
          id?: string
          instructor?: string | null
          nombre_modulo?: string
          orden?: number
          peso_evaluacion?: number | null
          recursos_url?: string[] | null
          updated_at?: string
        }
        Relationships: [
          {
            foreignKeyName: "modulos_diplomados_diplomado_id_fkey"
            columns: ["diplomado_id"]
            isOneToOne: false
            referencedRelation: "diplomados"
            referencedColumns: ["id"]
          },
        ]
      }
      organizaciones: {
        Row: {
          actas_fundacion_url: string | null
          ciudad: string | null
          codigo: string
          created_at: string | null
          direccion: string | null
          email: string | null
          estado_adecuacion:
            | Database["public"]["Enums"]["estado_adecuacion"]
            | null
          estatutos_url: string | null
          fecha_fundacion: string | null
          id: string
          miembros_minimos: number | null
          nombre: string
          organizacion_padre_id: string | null
          pais: string | null
          provincia: string | null
          telefono: string | null
          tipo: Database["public"]["Enums"]["tipo_organizacion"]
          updated_at: string | null
        }
        Insert: {
          actas_fundacion_url?: string | null
          ciudad?: string | null
          codigo: string
          created_at?: string | null
          direccion?: string | null
          email?: string | null
          estado_adecuacion?:
            | Database["public"]["Enums"]["estado_adecuacion"]
            | null
          estatutos_url?: string | null
          fecha_fundacion?: string | null
          id?: string
          miembros_minimos?: number | null
          nombre: string
          organizacion_padre_id?: string | null
          pais?: string | null
          provincia?: string | null
          telefono?: string | null
          tipo: Database["public"]["Enums"]["tipo_organizacion"]
          updated_at?: string | null
        }
        Update: {
          actas_fundacion_url?: string | null
          ciudad?: string | null
          codigo?: string
          created_at?: string | null
          direccion?: string | null
          email?: string | null
          estado_adecuacion?:
            | Database["public"]["Enums"]["estado_adecuacion"]
            | null
          estatutos_url?: string | null
          fecha_fundacion?: string | null
          id?: string
          miembros_minimos?: number | null
          nombre?: string
          organizacion_padre_id?: string | null
          pais?: string | null
          provincia?: string | null
          telefono?: string | null
          tipo?: Database["public"]["Enums"]["tipo_organizacion"]
          updated_at?: string | null
        }
        Relationships: [
          {
            foreignKeyName: "organizaciones_organizacion_padre_id_fkey"
            columns: ["organizacion_padre_id"]
            isOneToOne: false
            referencedRelation: "organizaciones"
            referencedColumns: ["id"]
          },
        ]
      }
      organos_cldc: {
        Row: {
          activo: boolean | null
          created_at: string | null
          descripcion: string | null
          funciones: string[] | null
          id: string
          nivel_jerarquico: number | null
          nombre: string
          organizacion_id: string | null
          tipo_organo: string
          updated_at: string | null
        }
        Insert: {
          activo?: boolean | null
          created_at?: string | null
          descripcion?: string | null
          funciones?: string[] | null
          id?: string
          nivel_jerarquico?: number | null
          nombre: string
          organizacion_id?: string | null
          tipo_organo: string
          updated_at?: string | null
        }
        Update: {
          activo?: boolean | null
          created_at?: string | null
          descripcion?: string | null
          funciones?: string[] | null
          id?: string
          nivel_jerarquico?: number | null
          nombre?: string
          organizacion_id?: string | null
          tipo_organo?: string
          updated_at?: string | null
        }
        Relationships: [
          {
            foreignKeyName: "organos_cldc_organizacion_id_fkey"
            columns: ["organizacion_id"]
            isOneToOne: false
            referencedRelation: "organizaciones"
            referencedColumns: ["id"]
          },
        ]
      }
      padrones_electorales: {
        Row: {
          activo: boolean | null
          created_at: string | null
          created_by: string | null
          descripcion: string | null
          fecha_fin: string
          fecha_inicio: string
          id: string
          organizacion_id: string
          periodo: string
          total_electores: number | null
        }
        Insert: {
          activo?: boolean | null
          created_at?: string | null
          created_by?: string | null
          descripcion?: string | null
          fecha_fin: string
          fecha_inicio: string
          id?: string
          organizacion_id: string
          periodo: string
          total_electores?: number | null
        }
        Update: {
          activo?: boolean | null
          created_at?: string | null
          created_by?: string | null
          descripcion?: string | null
          fecha_fin?: string
          fecha_inicio?: string
          id?: string
          organizacion_id?: string
          periodo?: string
          total_electores?: number | null
        }
        Relationships: [
          {
            foreignKeyName: "padrones_electorales_organizacion_id_fkey"
            columns: ["organizacion_id"]
            isOneToOne: false
            referencedRelation: "organizaciones"
            referencedColumns: ["id"]
          },
        ]
      }
      periodos_directiva: {
        Row: {
          acta_eleccion_url: string | null
          activo: boolean | null
          created_at: string | null
          directiva: Json | null
          fecha_fin: string
          fecha_inicio: string
          id: string
          organizacion_id: string
        }
        Insert: {
          acta_eleccion_url?: string | null
          activo?: boolean | null
          created_at?: string | null
          directiva?: Json | null
          fecha_fin: string
          fecha_inicio: string
          id?: string
          organizacion_id: string
        }
        Update: {
          acta_eleccion_url?: string | null
          activo?: boolean | null
          created_at?: string | null
          directiva?: Json | null
          fecha_fin?: string
          fecha_inicio?: string
          id?: string
          organizacion_id?: string
        }
        Relationships: [
          {
            foreignKeyName: "periodos_directiva_organizacion_id_fkey"
            columns: ["organizacion_id"]
            isOneToOne: false
            referencedRelation: "organizaciones"
            referencedColumns: ["id"]
          },
        ]
      }
      presupuestos: {
        Row: {
          activo: boolean | null
          categoria: string
          created_at: string | null
          created_by: string | null
          id: string
          monto_ejecutado: number | null
          monto_presupuestado: number
          organizacion_id: string
          periodo: string
        }
        Insert: {
          activo?: boolean | null
          categoria: string
          created_at?: string | null
          created_by?: string | null
          id?: string
          monto_ejecutado?: number | null
          monto_presupuestado: number
          organizacion_id: string
          periodo: string
        }
        Update: {
          activo?: boolean | null
          categoria?: string
          created_at?: string | null
          created_by?: string | null
          id?: string
          monto_ejecutado?: number | null
          monto_presupuestado?: number
          organizacion_id?: string
          periodo?: string
        }
        Relationships: [
          {
            foreignKeyName: "presupuestos_organizacion_id_fkey"
            columns: ["organizacion_id"]
            isOneToOne: false
            referencedRelation: "organizaciones"
            referencedColumns: ["id"]
          },
        ]
      }
      profiles: {
        Row: {
          avatar_url: string | null
          created_at: string | null
          email: string
          id: string
          nombre_completo: string
          telefono: string | null
          updated_at: string | null
        }
        Insert: {
          avatar_url?: string | null
          created_at?: string | null
          email: string
          id: string
          nombre_completo: string
          telefono?: string | null
          updated_at?: string | null
        }
        Update: {
          avatar_url?: string | null
          created_at?: string | null
          email?: string
          id?: string
          nombre_completo?: string
          telefono?: string | null
          updated_at?: string | null
        }
        Relationships: []
      }
      route_stops: {
        Row: {
          actual_arrival: string | null
          actual_duration_minutes: number | null
          created_at: string | null
          estimated_arrival: string | null
          estimated_duration_minutes: number | null
          failure_reason: string | null
          id: string
          order_id: string
          proof_of_delivery_url: string | null
          route_id: string
          signature_url: string | null
          status: string | null
          stop_order: number
          updated_at: string | null
        }
        Insert: {
          actual_arrival?: string | null
          actual_duration_minutes?: number | null
          created_at?: string | null
          estimated_arrival?: string | null
          estimated_duration_minutes?: number | null
          failure_reason?: string | null
          id?: string
          order_id: string
          proof_of_delivery_url?: string | null
          route_id: string
          signature_url?: string | null
          status?: string | null
          stop_order: number
          updated_at?: string | null
        }
        Update: {
          actual_arrival?: string | null
          actual_duration_minutes?: number | null
          created_at?: string | null
          estimated_arrival?: string | null
          estimated_duration_minutes?: number | null
          failure_reason?: string | null
          id?: string
          order_id?: string
          proof_of_delivery_url?: string | null
          route_id?: string
          signature_url?: string | null
          status?: string | null
          stop_order?: number
          updated_at?: string | null
        }
        Relationships: [
          {
            foreignKeyName: "route_stops_order_id_fkey"
            columns: ["order_id"]
            isOneToOne: false
            referencedRelation: "delivery_orders"
            referencedColumns: ["id"]
          },
          {
            foreignKeyName: "route_stops_route_id_fkey"
            columns: ["route_id"]
            isOneToOne: false
            referencedRelation: "delivery_routes"
            referencedColumns: ["id"]
          },
        ]
      }
      seccional_submissions: {
        Row: {
          actas_paths: string[] | null
          created_at: string
          created_by: string
          directiva: string | null
          id: string
          miembros_contados: number
          miembros_csv_path: string | null
          miembros_min_ok: boolean
          observaciones: string | null
          seccional_nombre: string
        }
        Insert: {
          actas_paths?: string[] | null
          created_at?: string
          created_by: string
          directiva?: string | null
          id?: string
          miembros_contados?: number
          miembros_csv_path?: string | null
          miembros_min_ok?: boolean
          observaciones?: string | null
          seccional_nombre: string
        }
        Update: {
          actas_paths?: string[] | null
          created_at?: string
          created_by?: string
          directiva?: string | null
          id?: string
          miembros_contados?: number
          miembros_csv_path?: string | null
          miembros_min_ok?: boolean
          observaciones?: string | null
          seccional_nombre?: string
        }
        Relationships: []
      }
      seccionales: {
        Row: {
          ciudad: string | null
          coordinador_id: string | null
          created_at: string | null
          direccion: string | null
          email: string | null
          estado: string | null
          fecha_fundacion: string | null
          id: string
          miembros_count: number | null
          nombre: string
          organizacion_id: string | null
          pais: string | null
          provincia: string | null
          telefono: string | null
          tipo: string
          updated_at: string | null
        }
        Insert: {
          ciudad?: string | null
          coordinador_id?: string | null
          created_at?: string | null
          direccion?: string | null
          email?: string | null
          estado?: string | null
          fecha_fundacion?: string | null
          id?: string
          miembros_count?: number | null
          nombre: string
          organizacion_id?: string | null
          pais?: string | null
          provincia?: string | null
          telefono?: string | null
          tipo: string
          updated_at?: string | null
        }
        Update: {
          ciudad?: string | null
          coordinador_id?: string | null
          created_at?: string | null
          direccion?: string | null
          email?: string | null
          estado?: string | null
          fecha_fundacion?: string | null
          id?: string
          miembros_count?: number | null
          nombre?: string
          organizacion_id?: string | null
          pais?: string | null
          provincia?: string | null
          telefono?: string | null
          tipo?: string
          updated_at?: string | null
        }
        Relationships: [
          {
            foreignKeyName: "seccionales_coordinador_id_fkey"
            columns: ["coordinador_id"]
            isOneToOne: false
            referencedRelation: "miembros"
            referencedColumns: ["id"]
          },
          {
            foreignKeyName: "seccionales_organizacion_id_fkey"
            columns: ["organizacion_id"]
            isOneToOne: false
            referencedRelation: "organizaciones"
            referencedColumns: ["id"]
          },
        ]
      }
      security_audit_log: {
        Row: {
          action: string
          additional_data: Json | null
          created_at: string | null
          error_message: string | null
          id: string
          ip_address: unknown | null
          resource_id: string | null
          resource_type: string
          success: boolean | null
          user_agent: string | null
          user_id: string | null
        }
        Insert: {
          action: string
          additional_data?: Json | null
          created_at?: string | null
          error_message?: string | null
          id?: string
          ip_address?: unknown | null
          resource_id?: string | null
          resource_type: string
          success?: boolean | null
          user_agent?: string | null
          user_id?: string | null
        }
        Update: {
          action?: string
          additional_data?: Json | null
          created_at?: string | null
          error_message?: string | null
          id?: string
          ip_address?: unknown | null
          resource_id?: string | null
          resource_type?: string
          success?: boolean | null
          user_agent?: string | null
          user_id?: string | null
        }
        Relationships: []
      }
      sensitive_data_access_audit: {
        Row: {
          access_timestamp: string | null
          access_type: string
          accessed_fields: string[]
          accessed_member_id: string
          accessing_user_id: string
          id: string
          ip_address: unknown | null
          justification: string | null
          user_agent: string | null
        }
        Insert: {
          access_timestamp?: string | null
          access_type: string
          accessed_fields: string[]
          accessed_member_id: string
          accessing_user_id: string
          id?: string
          ip_address?: unknown | null
          justification?: string | null
          user_agent?: string | null
        }
        Update: {
          access_timestamp?: string | null
          access_type?: string
          accessed_fields?: string[]
          accessed_member_id?: string
          accessing_user_id?: string
          id?: string
          ip_address?: unknown | null
          justification?: string | null
          user_agent?: string | null
        }
        Relationships: []
      }
      sensitive_data_access_log: {
        Row: {
          access_timestamp: string | null
          access_type: string
          accessed_member_id: string
          accessing_user_id: string
          approved_by: string | null
          id: string
          ip_address: unknown | null
          justification: string | null
          user_agent: string | null
        }
        Insert: {
          access_timestamp?: string | null
          access_type: string
          accessed_member_id: string
          accessing_user_id: string
          approved_by?: string | null
          id?: string
          ip_address?: unknown | null
          justification?: string | null
          user_agent?: string | null
        }
        Update: {
          access_timestamp?: string | null
          access_type?: string
          accessed_member_id?: string
          accessing_user_id?: string
          approved_by?: string | null
          id?: string
          ip_address?: unknown | null
          justification?: string | null
          user_agent?: string | null
        }
        Relationships: []
      }
      transacciones_financieras: {
        Row: {
          aprobado_por: string | null
          categoria: string
          comprobante_url: string | null
          concepto: string
          created_at: string | null
          created_by: string | null
          fecha: string
          id: string
          metodo_pago: string | null
          monto: number
          observaciones: string | null
          organizacion_id: string
          referencia: string | null
          tipo: string
        }
        Insert: {
          aprobado_por?: string | null
          categoria: string
          comprobante_url?: string | null
          concepto: string
          created_at?: string | null
          created_by?: string | null
          fecha: string
          id?: string
          metodo_pago?: string | null
          monto: number
          observaciones?: string | null
          organizacion_id: string
          referencia?: string | null
          tipo: string
        }
        Update: {
          aprobado_por?: string | null
          categoria?: string
          comprobante_url?: string | null
          concepto?: string
          created_at?: string | null
          created_by?: string | null
          fecha?: string
          id?: string
          metodo_pago?: string | null
          monto?: number
          observaciones?: string | null
          organizacion_id?: string
          referencia?: string | null
          tipo?: string
        }
        Relationships: [
          {
            foreignKeyName: "transacciones_financieras_organizacion_id_fkey"
            columns: ["organizacion_id"]
            isOneToOne: false
            referencedRelation: "organizaciones"
            referencedColumns: ["id"]
          },
        ]
      }
      user_roles: {
        Row: {
          created_at: string | null
          id: string
          organizacion_id: string | null
          role: Database["public"]["Enums"]["app_role"]
          user_id: string
        }
        Insert: {
          created_at?: string | null
          id?: string
          organizacion_id?: string | null
          role: Database["public"]["Enums"]["app_role"]
          user_id: string
        }
        Update: {
          created_at?: string | null
          id?: string
          organizacion_id?: string | null
          role?: Database["public"]["Enums"]["app_role"]
          user_id?: string
        }
        Relationships: []
      }
      vehicles: {
        Row: {
          capacity_volume: number | null
          capacity_weight: number | null
          company_id: string
          created_at: string | null
          driver_id: string | null
          id: string
          is_active: boolean | null
          model: string | null
          plate: string
          updated_at: string | null
        }
        Insert: {
          capacity_volume?: number | null
          capacity_weight?: number | null
          company_id: string
          created_at?: string | null
          driver_id?: string | null
          id?: string
          is_active?: boolean | null
          model?: string | null
          plate: string
          updated_at?: string | null
        }
        Update: {
          capacity_volume?: number | null
          capacity_weight?: number | null
          company_id?: string
          created_at?: string | null
          driver_id?: string | null
          id?: string
          is_active?: boolean | null
          model?: string | null
          plate?: string
          updated_at?: string | null
        }
        Relationships: [
          {
            foreignKeyName: "vehicles_company_id_fkey"
            columns: ["company_id"]
            isOneToOne: false
            referencedRelation: "delivery_companies"
            referencedColumns: ["id"]
          },
          {
            foreignKeyName: "vehicles_driver_id_fkey"
            columns: ["driver_id"]
            isOneToOne: false
            referencedRelation: "drivers"
            referencedColumns: ["id"]
          },
        ]
      }
      votos: {
        Row: {
          candidato_id: string | null
          eleccion_id: string
          elector_id: string
          id: string
          modalidad: string | null
          timestamp_voto: string | null
          verificado: boolean | null
          voto_hash: string
        }
        Insert: {
          candidato_id?: string | null
          eleccion_id: string
          elector_id: string
          id?: string
          modalidad?: string | null
          timestamp_voto?: string | null
          verificado?: boolean | null
          voto_hash: string
        }
        Update: {
          candidato_id?: string | null
          eleccion_id?: string
          elector_id?: string
          id?: string
          modalidad?: string | null
          timestamp_voto?: string | null
          verificado?: boolean | null
          voto_hash?: string
        }
        Relationships: [
          {
            foreignKeyName: "votos_eleccion_id_fkey"
            columns: ["eleccion_id"]
            isOneToOne: false
            referencedRelation: "elecciones"
            referencedColumns: ["id"]
          },
          {
            foreignKeyName: "votos_elector_id_fkey"
            columns: ["elector_id"]
            isOneToOne: false
            referencedRelation: "electores"
            referencedColumns: ["id"]
          },
        ]
      }
    }
    Views: {
      [_ in never]: never
    }
    Functions: {
      get_budget_summary: {
        Args: { org_id: string }
        Returns: {
          categoria: string
          organizacion_id: string
          periodo: string
          presupuesto_total: number
        }[]
      }
      get_dashboard_stats: {
        Args: Record<PropertyKey, never>
        Returns: {
          total_miembros_activos: number
          total_organizaciones: number
        }[]
      }
      get_financial_summary: {
        Args: { org_id: string }
        Returns: {
          categoria: string
          organizacion_id: string
          periodo: string
          total_gastos: number
          total_ingresos: number
        }[]
      }
      get_member_sensitive_data: {
        Args: { justification_param?: string; member_id_param: string }
        Returns: {
          cedula: string
          direccion: string
          email: string
          fecha_nacimiento: string
          id: string
          telefono: string
        }[]
      }
      get_member_stats_by_province: {
        Args: { requesting_user_id: string }
        Returns: {
          active_count: number
          member_count: number
          provincia: string
        }[]
      }
      get_members_for_moderators: {
        Args: { org_id: string }
        Returns: {
          created_at: string
          email_masked: string
          estado_membresia: Database["public"]["Enums"]["estado_membresia"]
          fecha_ingreso: string
          fecha_vencimiento: string
          foto_url: string
          id: string
          nombre_completo: string
          numero_carnet: string
          organizacion_id: string
          profesion: string
          telefono_masked: string
          updated_at: string
        }[]
      }
      get_miembro_directivo_contact_details: {
        Args: { miembro_directivo_id: string }
        Returns: {
          email_institucional: string
          id: string
          telefono_institucional: string
        }[]
      }
      get_miembros_public_only: {
        Args: { org_id?: string }
        Returns: {
          cedula: string
          created_at: string
          direccion: string
          email: string
          estado_membresia: Database["public"]["Enums"]["estado_membresia"]
          fecha_ingreso: string
          fecha_nacimiento: string
          fecha_vencimiento: string
          foto_url: string
          id: string
          nombre_completo: string
          numero_carnet: string
          observaciones: string
          organizacion_id: string
          profesion: string
          telefono: string
          updated_at: string
          user_id: string
        }[]
      }
      get_public_members: {
        Args: { org_id?: string }
        Returns: {
          estado_membresia: Database["public"]["Enums"]["estado_membresia"]
          fecha_ingreso: string
          foto_url: string
          id: string
          nombre_completo: string
          numero_carnet: string
          organizacion_id: string
          profesion: string
        }[]
      }
      get_public_miembros_directivos: {
        Args: { org_id?: string }
        Returns: {
          cargo_id: string
          created_at: string
          es_presidente: boolean
          estado: string
          fecha_fin: string
          fecha_inicio: string
          foto_url: string
          id: string
          miembro_id: string
          organo_id: string
          periodo: string
          semblanza: string
          updated_at: string
        }[]
      }
      get_public_seccionales: {
        Args: Record<PropertyKey, never>
        Returns: {
          ciudad: string
          created_at: string
          estado: string
          fecha_fundacion: string
          id: string
          miembros_count: number
          nombre: string
          organizacion_id: string
          pais: string
          provincia: string
          tipo: string
          updated_at: string
        }[]
      }
      get_safe_member_info: {
        Args: { org_id: string }
        Returns: {
          estado_membresia: Database["public"]["Enums"]["estado_membresia"]
          id: string
          nombre_completo: string
          numero_carnet: string
          organizacion_id: string
          profesion: string
        }[]
      }
      get_seccional_contact_details: {
        Args: { seccional_id: string }
        Returns: {
          direccion: string
          email: string
          id: string
          telefono: string
        }[]
      }
      grant_sensitive_access: {
        Args: { justification_param: string; member_id_param: string }
        Returns: {
          cedula: string
          direccion: string
          email: string
          fecha_nacimiento: string
          id: string
          telefono: string
        }[]
      }
      has_role: {
        Args: {
          _org_id?: string
          _role: Database["public"]["Enums"]["app_role"]
          _user_id: string
        }
        Returns: boolean
      }
      increment_vote_count: {
        Args: { election_id: string }
        Returns: undefined
      }
      log_member_access: {
        Args: { access_type: string; accessed_member_id: string }
        Returns: undefined
      }
      log_security_event: {
        Args: {
          p_action: string
          p_additional_data?: Json
          p_error_message?: string
          p_resource_id?: string
          p_resource_type: string
          p_success?: boolean
        }
        Returns: undefined
      }
      mask_address: {
        Args: { address_value: string }
        Returns: string
      }
      mask_cedula: {
        Args: { cedula_value: string }
        Returns: string
      }
      mask_email: {
        Args: { email_value: string }
        Returns: string
      }
      mask_phone: {
        Args: { phone_value: string }
        Returns: string
      }
      user_can_access_driver_company: {
        Args: { driver_company_id: string }
        Returns: boolean
      }
      user_organizations: {
        Args: { _user_id: string }
        Returns: {
          org_id: string
          role: Database["public"]["Enums"]["app_role"]
        }[]
      }
    }
    Enums: {
      app_role: "admin" | "moderador" | "miembro"
      estado_adecuacion: "pendiente" | "en_revision" | "aprobada" | "rechazada"
      estado_membresia: "activa" | "vencida" | "pendiente" | "suspendida"
      tipo_membresia:
        | "fundador"
        | "activo"
        | "pasivo"
        | "honorifico"
        | "estudiante"
        | "diaspora"
      tipo_organizacion:
        | "filial"
        | "seccional"
        | "delegacion"
        | "seccional_nacional"
        | "seccional_internacional"
        | "asociacion"
        | "gremio"
        | "sindicato"
        | "otra_entidad"
    }
    CompositeTypes: {
      [_ in never]: never
    }
  }
}

type DatabaseWithoutInternals = Omit<Database, "__InternalSupabase">

type DefaultSchema = DatabaseWithoutInternals[Extract<keyof Database, "public">]

export type Tables<
  DefaultSchemaTableNameOrOptions extends
    | keyof (DefaultSchema["Tables"] & DefaultSchema["Views"])
    | { schema: keyof DatabaseWithoutInternals },
  TableName extends DefaultSchemaTableNameOrOptions extends {
    schema: keyof DatabaseWithoutInternals
  }
    ? keyof (DatabaseWithoutInternals[DefaultSchemaTableNameOrOptions["schema"]]["Tables"] &
        DatabaseWithoutInternals[DefaultSchemaTableNameOrOptions["schema"]]["Views"])
    : never = never,
> = DefaultSchemaTableNameOrOptions extends {
  schema: keyof DatabaseWithoutInternals
}
  ? (DatabaseWithoutInternals[DefaultSchemaTableNameOrOptions["schema"]]["Tables"] &
      DatabaseWithoutInternals[DefaultSchemaTableNameOrOptions["schema"]]["Views"])[TableName] extends {
      Row: infer R
    }
    ? R
    : never
  : DefaultSchemaTableNameOrOptions extends keyof (DefaultSchema["Tables"] &
        DefaultSchema["Views"])
    ? (DefaultSchema["Tables"] &
        DefaultSchema["Views"])[DefaultSchemaTableNameOrOptions] extends {
        Row: infer R
      }
      ? R
      : never
    : never

export type TablesInsert<
  DefaultSchemaTableNameOrOptions extends
    | keyof DefaultSchema["Tables"]
    | { schema: keyof DatabaseWithoutInternals },
  TableName extends DefaultSchemaTableNameOrOptions extends {
    schema: keyof DatabaseWithoutInternals
  }
    ? keyof DatabaseWithoutInternals[DefaultSchemaTableNameOrOptions["schema"]]["Tables"]
    : never = never,
> = DefaultSchemaTableNameOrOptions extends {
  schema: keyof DatabaseWithoutInternals
}
  ? DatabaseWithoutInternals[DefaultSchemaTableNameOrOptions["schema"]]["Tables"][TableName] extends {
      Insert: infer I
    }
    ? I
    : never
  : DefaultSchemaTableNameOrOptions extends keyof DefaultSchema["Tables"]
    ? DefaultSchema["Tables"][DefaultSchemaTableNameOrOptions] extends {
        Insert: infer I
      }
      ? I
      : never
    : never

export type TablesUpdate<
  DefaultSchemaTableNameOrOptions extends
    | keyof DefaultSchema["Tables"]
    | { schema: keyof DatabaseWithoutInternals },
  TableName extends DefaultSchemaTableNameOrOptions extends {
    schema: keyof DatabaseWithoutInternals
  }
    ? keyof DatabaseWithoutInternals[DefaultSchemaTableNameOrOptions["schema"]]["Tables"]
    : never = never,
> = DefaultSchemaTableNameOrOptions extends {
  schema: keyof DatabaseWithoutInternals
}
  ? DatabaseWithoutInternals[DefaultSchemaTableNameOrOptions["schema"]]["Tables"][TableName] extends {
      Update: infer U
    }
    ? U
    : never
  : DefaultSchemaTableNameOrOptions extends keyof DefaultSchema["Tables"]
    ? DefaultSchema["Tables"][DefaultSchemaTableNameOrOptions] extends {
        Update: infer U
      }
      ? U
      : never
    : never

export type Enums<
  DefaultSchemaEnumNameOrOptions extends
    | keyof DefaultSchema["Enums"]
    | { schema: keyof DatabaseWithoutInternals },
  EnumName extends DefaultSchemaEnumNameOrOptions extends {
    schema: keyof DatabaseWithoutInternals
  }
    ? keyof DatabaseWithoutInternals[DefaultSchemaEnumNameOrOptions["schema"]]["Enums"]
    : never = never,
> = DefaultSchemaEnumNameOrOptions extends {
  schema: keyof DatabaseWithoutInternals
}
  ? DatabaseWithoutInternals[DefaultSchemaEnumNameOrOptions["schema"]]["Enums"][EnumName]
  : DefaultSchemaEnumNameOrOptions extends keyof DefaultSchema["Enums"]
    ? DefaultSchema["Enums"][DefaultSchemaEnumNameOrOptions]
    : never

export type CompositeTypes<
  PublicCompositeTypeNameOrOptions extends
    | keyof DefaultSchema["CompositeTypes"]
    | { schema: keyof DatabaseWithoutInternals },
  CompositeTypeName extends PublicCompositeTypeNameOrOptions extends {
    schema: keyof DatabaseWithoutInternals
  }
    ? keyof DatabaseWithoutInternals[PublicCompositeTypeNameOrOptions["schema"]]["CompositeTypes"]
    : never = never,
> = PublicCompositeTypeNameOrOptions extends {
  schema: keyof DatabaseWithoutInternals
}
  ? DatabaseWithoutInternals[PublicCompositeTypeNameOrOptions["schema"]]["CompositeTypes"][CompositeTypeName]
  : PublicCompositeTypeNameOrOptions extends keyof DefaultSchema["CompositeTypes"]
    ? DefaultSchema["CompositeTypes"][PublicCompositeTypeNameOrOptions]
    : never

export const Constants = {
  public: {
    Enums: {
      app_role: ["admin", "moderador", "miembro"],
      estado_adecuacion: ["pendiente", "en_revision", "aprobada", "rechazada"],
      estado_membresia: ["activa", "vencida", "pendiente", "suspendida"],
      tipo_membresia: [
        "fundador",
        "activo",
        "pasivo",
        "honorifico",
        "estudiante",
        "diaspora",
      ],
      tipo_organizacion: [
        "filial",
        "seccional",
        "delegacion",
        "seccional_nacional",
        "seccional_internacional",
        "asociacion",
        "gremio",
        "sindicato",
        "otra_entidad",
      ],
    },
  },
} as const
