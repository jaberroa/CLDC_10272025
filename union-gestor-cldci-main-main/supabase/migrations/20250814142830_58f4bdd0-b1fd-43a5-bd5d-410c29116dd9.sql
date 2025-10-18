-- Create delivery management system tables

-- Companies/Organizations for delivery services
CREATE TABLE public.delivery_companies (
  id uuid NOT NULL DEFAULT gen_random_uuid() PRIMARY KEY,
  name text NOT NULL,
  logo_url text,
  primary_color text DEFAULT '#3B82F6',
  secondary_color text DEFAULT '#1E40AF',
  email text,
  phone text,
  address text,
  created_at timestamp with time zone DEFAULT now(),
  updated_at timestamp with time zone DEFAULT now()
);

-- Drivers
CREATE TABLE public.drivers (
  id uuid NOT NULL DEFAULT gen_random_uuid() PRIMARY KEY,
  user_id uuid REFERENCES auth.users(id) ON DELETE CASCADE,
  company_id uuid REFERENCES public.delivery_companies(id) ON DELETE CASCADE NOT NULL,
  name text NOT NULL,
  email text NOT NULL,
  phone text,
  photo_url text,
  license_number text,
  is_active boolean DEFAULT true,
  current_lat numeric,
  current_lng numeric,
  last_location_update timestamp with time zone,
  created_at timestamp with time zone DEFAULT now(),
  updated_at timestamp with time zone DEFAULT now()
);

-- Vehicles
CREATE TABLE public.vehicles (
  id uuid NOT NULL DEFAULT gen_random_uuid() PRIMARY KEY,
  company_id uuid REFERENCES public.delivery_companies(id) ON DELETE CASCADE NOT NULL,
  driver_id uuid REFERENCES public.drivers(id) ON DELETE SET NULL,
  plate text NOT NULL,
  model text,
  capacity_weight numeric DEFAULT 0,
  capacity_volume numeric DEFAULT 0,
  is_active boolean DEFAULT true,
  created_at timestamp with time zone DEFAULT now(),
  updated_at timestamp with time zone DEFAULT now()
);

-- Customers
CREATE TABLE public.customers (
  id uuid NOT NULL DEFAULT gen_random_uuid() PRIMARY KEY,
  company_id uuid REFERENCES public.delivery_companies(id) ON DELETE CASCADE NOT NULL,
  name text NOT NULL,
  email text,
  phone text,
  address text,
  lat numeric,
  lng numeric,
  created_at timestamp with time zone DEFAULT now(),
  updated_at timestamp with time zone DEFAULT now()
);

-- Orders/Deliveries
CREATE TABLE public.delivery_orders (
  id uuid NOT NULL DEFAULT gen_random_uuid() PRIMARY KEY,
  company_id uuid REFERENCES public.delivery_companies(id) ON DELETE CASCADE NOT NULL,
  customer_id uuid REFERENCES public.customers(id) ON DELETE CASCADE NOT NULL,
  order_number text NOT NULL UNIQUE,
  pickup_address text,
  pickup_lat numeric,
  pickup_lng numeric,
  delivery_address text NOT NULL,
  delivery_lat numeric,
  delivery_lng numeric,
  preferred_time_start timestamp with time zone,
  preferred_time_end timestamp with time zone,
  priority text DEFAULT 'normal' CHECK (priority IN ('low', 'normal', 'high', 'urgent')),
  weight numeric DEFAULT 0,
  volume numeric DEFAULT 0,
  notes text,
  delivery_instructions text,
  requires_signature boolean DEFAULT false,
  requires_pin boolean DEFAULT false,
  pin_code text,
  status text DEFAULT 'pending' CHECK (status IN ('pending', 'assigned', 'in_transit', 'delivered', 'failed', 'cancelled')),
  tracking_token text UNIQUE DEFAULT gen_random_uuid(),
  created_at timestamp with time zone DEFAULT now(),
  updated_at timestamp with time zone DEFAULT now()
);

-- Routes
CREATE TABLE public.delivery_routes (
  id uuid NOT NULL DEFAULT gen_random_uuid() PRIMARY KEY,
  company_id uuid REFERENCES public.delivery_companies(id) ON DELETE CASCADE NOT NULL,
  driver_id uuid REFERENCES public.drivers(id) ON DELETE SET NULL,
  vehicle_id uuid REFERENCES public.vehicles(id) ON DELETE SET NULL,
  route_name text NOT NULL,
  status text DEFAULT 'planned' CHECK (status IN ('planned', 'active', 'completed', 'cancelled')),
  planned_start_time timestamp with time zone,
  actual_start_time timestamp with time zone,
  planned_end_time timestamp with time zone,
  actual_end_time timestamp with time zone,
  total_distance numeric DEFAULT 0,
  total_duration_minutes integer DEFAULT 0,
  optimization_data jsonb,
  created_at timestamp with time zone DEFAULT now(),
  updated_at timestamp with time zone DEFAULT now()
);

-- Route stops (orders in a route)
CREATE TABLE public.route_stops (
  id uuid NOT NULL DEFAULT gen_random_uuid() PRIMARY KEY,
  route_id uuid REFERENCES public.delivery_routes(id) ON DELETE CASCADE NOT NULL,
  order_id uuid REFERENCES public.delivery_orders(id) ON DELETE CASCADE NOT NULL,
  stop_order integer NOT NULL,
  estimated_arrival timestamp with time zone,
  actual_arrival timestamp with time zone,
  estimated_duration_minutes integer DEFAULT 15,
  actual_duration_minutes integer,
  status text DEFAULT 'pending' CHECK (status IN ('pending', 'in_progress', 'completed', 'failed', 'skipped')),
  failure_reason text,
  proof_of_delivery_url text,
  signature_url text,
  created_at timestamp with time zone DEFAULT now(),
  updated_at timestamp with time zone DEFAULT now(),
  UNIQUE(route_id, stop_order)
);

-- Messages between operators and drivers
CREATE TABLE public.driver_messages (
  id uuid NOT NULL DEFAULT gen_random_uuid() PRIMARY KEY,
  company_id uuid REFERENCES public.delivery_companies(id) ON DELETE CASCADE NOT NULL,
  driver_id uuid REFERENCES public.drivers(id) ON DELETE CASCADE NOT NULL,
  sender_id uuid REFERENCES auth.users(id) ON DELETE CASCADE NOT NULL,
  message text NOT NULL,
  is_read boolean DEFAULT false,
  created_at timestamp with time zone DEFAULT now()
);

-- Delivery feedback and ratings
CREATE TABLE public.delivery_feedback (
  id uuid NOT NULL DEFAULT gen_random_uuid() PRIMARY KEY,
  order_id uuid REFERENCES public.delivery_orders(id) ON DELETE CASCADE NOT NULL,
  rating integer CHECK (rating >= 1 AND rating <= 5),
  comment text,
  created_at timestamp with time zone DEFAULT now()
);

-- Create indexes for performance
CREATE INDEX idx_drivers_company_id ON public.drivers(company_id);
CREATE INDEX idx_drivers_user_id ON public.drivers(user_id);
CREATE INDEX idx_vehicles_company_id ON public.vehicles(company_id);
CREATE INDEX idx_vehicles_driver_id ON public.vehicles(driver_id);
CREATE INDEX idx_customers_company_id ON public.customers(company_id);
CREATE INDEX idx_delivery_orders_company_id ON public.delivery_orders(company_id);
CREATE INDEX idx_delivery_orders_customer_id ON public.delivery_orders(customer_id);
CREATE INDEX idx_delivery_orders_status ON public.delivery_orders(status);
CREATE INDEX idx_delivery_orders_tracking_token ON public.delivery_orders(tracking_token);
CREATE INDEX idx_delivery_routes_company_id ON public.delivery_routes(company_id);
CREATE INDEX idx_delivery_routes_driver_id ON public.delivery_routes(driver_id);
CREATE INDEX idx_route_stops_route_id ON public.route_stops(route_id);
CREATE INDEX idx_route_stops_order_id ON public.route_stops(order_id);
CREATE INDEX idx_driver_messages_driver_id ON public.driver_messages(driver_id);

-- Enable RLS on all tables
ALTER TABLE public.delivery_companies ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.drivers ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.vehicles ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.customers ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.delivery_orders ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.delivery_routes ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.route_stops ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.driver_messages ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.delivery_feedback ENABLE ROW LEVEL SECURITY;

-- Create RLS policies for delivery companies
CREATE POLICY "Users can view their company" ON public.delivery_companies
  FOR SELECT USING (
    EXISTS (
      SELECT 1 FROM public.drivers d 
      WHERE d.company_id = delivery_companies.id 
      AND d.user_id = auth.uid()
    )
  );

CREATE POLICY "Admins can manage companies" ON public.delivery_companies
  FOR ALL USING (
    EXISTS (
      SELECT 1 FROM public.user_roles ur 
      WHERE ur.user_id = auth.uid() 
      AND ur.role = 'admin'::app_role
    )
  );

-- Create RLS policies for drivers
CREATE POLICY "Drivers can view their own data" ON public.drivers
  FOR SELECT USING (user_id = auth.uid());

CREATE POLICY "Company operators can view their drivers" ON public.drivers
  FOR SELECT USING (
    EXISTS (
      SELECT 1 FROM public.drivers d2 
      WHERE d2.company_id = drivers.company_id 
      AND d2.user_id = auth.uid()
    )
  );

CREATE POLICY "Admins can manage all drivers" ON public.drivers
  FOR ALL USING (
    EXISTS (
      SELECT 1 FROM public.user_roles ur 
      WHERE ur.user_id = auth.uid() 
      AND ur.role = 'admin'::app_role
    )
  );

-- Create RLS policies for vehicles
CREATE POLICY "Company users can view their vehicles" ON public.vehicles
  FOR SELECT USING (
    EXISTS (
      SELECT 1 FROM public.drivers d 
      WHERE d.company_id = vehicles.company_id 
      AND d.user_id = auth.uid()
    )
  );

CREATE POLICY "Admins can manage all vehicles" ON public.vehicles
  FOR ALL USING (
    EXISTS (
      SELECT 1 FROM public.user_roles ur 
      WHERE ur.user_id = auth.uid() 
      AND ur.role = 'admin'::app_role
    )
  );

-- Create RLS policies for customers
CREATE POLICY "Company users can view their customers" ON public.customers
  FOR SELECT USING (
    EXISTS (
      SELECT 1 FROM public.drivers d 
      WHERE d.company_id = customers.company_id 
      AND d.user_id = auth.uid()
    )
  );

CREATE POLICY "Admins can manage all customers" ON public.customers
  FOR ALL USING (
    EXISTS (
      SELECT 1 FROM public.user_roles ur 
      WHERE ur.user_id = auth.uid() 
      AND ur.role = 'admin'::app_role
    )
  );

-- Create RLS policies for delivery orders
CREATE POLICY "Company users can view their orders" ON public.delivery_orders
  FOR SELECT USING (
    EXISTS (
      SELECT 1 FROM public.drivers d 
      WHERE d.company_id = delivery_orders.company_id 
      AND d.user_id = auth.uid()
    )
  );

CREATE POLICY "Drivers can update assigned orders" ON public.delivery_orders
  FOR UPDATE USING (
    EXISTS (
      SELECT 1 FROM public.delivery_routes dr
      JOIN public.route_stops rs ON dr.id = rs.route_id
      WHERE rs.order_id = delivery_orders.id
      AND dr.driver_id IN (
        SELECT id FROM public.drivers 
        WHERE user_id = auth.uid()
      )
    )
  );

CREATE POLICY "Admins can manage all orders" ON public.delivery_orders
  FOR ALL USING (
    EXISTS (
      SELECT 1 FROM public.user_roles ur 
      WHERE ur.user_id = auth.uid() 
      AND ur.role = 'admin'::app_role
    )
  );

-- Create RLS policies for delivery routes
CREATE POLICY "Company users can view their routes" ON public.delivery_routes
  FOR SELECT USING (
    EXISTS (
      SELECT 1 FROM public.drivers d 
      WHERE d.company_id = delivery_routes.company_id 
      AND d.user_id = auth.uid()
    )
  );

CREATE POLICY "Drivers can view their assigned routes" ON public.delivery_routes
  FOR SELECT USING (
    driver_id IN (
      SELECT id FROM public.drivers 
      WHERE user_id = auth.uid()
    )
  );

CREATE POLICY "Admins can manage all routes" ON public.delivery_routes
  FOR ALL USING (
    EXISTS (
      SELECT 1 FROM public.user_roles ur 
      WHERE ur.user_id = auth.uid() 
      AND ur.role = 'admin'::app_role
    )
  );

-- Create RLS policies for route stops
CREATE POLICY "Users can view stops for their routes" ON public.route_stops
  FOR SELECT USING (
    EXISTS (
      SELECT 1 FROM public.delivery_routes dr
      JOIN public.drivers d ON dr.driver_id = d.id
      WHERE dr.id = route_stops.route_id
      AND d.user_id = auth.uid()
    )
  );

CREATE POLICY "Drivers can update their route stops" ON public.route_stops
  FOR UPDATE USING (
    EXISTS (
      SELECT 1 FROM public.delivery_routes dr
      JOIN public.drivers d ON dr.driver_id = d.id
      WHERE dr.id = route_stops.route_id
      AND d.user_id = auth.uid()
    )
  );

CREATE POLICY "Admins can manage all route stops" ON public.route_stops
  FOR ALL USING (
    EXISTS (
      SELECT 1 FROM public.user_roles ur 
      WHERE ur.user_id = auth.uid() 
      AND ur.role = 'admin'::app_role
    )
  );

-- Create RLS policies for driver messages
CREATE POLICY "Drivers can view their messages" ON public.driver_messages
  FOR SELECT USING (
    driver_id IN (
      SELECT id FROM public.drivers 
      WHERE user_id = auth.uid()
    )
  );

CREATE POLICY "Company users can send messages to their drivers" ON public.driver_messages
  FOR INSERT WITH CHECK (
    EXISTS (
      SELECT 1 FROM public.drivers d1, public.drivers d2
      WHERE d1.id = driver_messages.driver_id
      AND d2.user_id = auth.uid()
      AND d1.company_id = d2.company_id
    )
  );

CREATE POLICY "Admins can manage all messages" ON public.driver_messages
  FOR ALL USING (
    EXISTS (
      SELECT 1 FROM public.user_roles ur 
      WHERE ur.user_id = auth.uid() 
      AND ur.role = 'admin'::app_role
    )
  );

-- Create RLS policies for delivery feedback
CREATE POLICY "Anyone can view feedback" ON public.delivery_feedback
  FOR SELECT USING (true);

CREATE POLICY "Anyone can create feedback" ON public.delivery_feedback
  FOR INSERT WITH CHECK (true);

-- Create trigger function for updating timestamps
CREATE OR REPLACE FUNCTION public.update_delivery_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = now();
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Create triggers for updated_at columns
CREATE TRIGGER update_delivery_companies_updated_at
    BEFORE UPDATE ON public.delivery_companies
    FOR EACH ROW
    EXECUTE FUNCTION public.update_delivery_updated_at_column();

CREATE TRIGGER update_drivers_updated_at
    BEFORE UPDATE ON public.drivers
    FOR EACH ROW
    EXECUTE FUNCTION public.update_delivery_updated_at_column();

CREATE TRIGGER update_vehicles_updated_at
    BEFORE UPDATE ON public.vehicles
    FOR EACH ROW
    EXECUTE FUNCTION public.update_delivery_updated_at_column();

CREATE TRIGGER update_customers_updated_at
    BEFORE UPDATE ON public.customers
    FOR EACH ROW
    EXECUTE FUNCTION public.update_delivery_updated_at_column();

CREATE TRIGGER update_delivery_orders_updated_at
    BEFORE UPDATE ON public.delivery_orders
    FOR EACH ROW
    EXECUTE FUNCTION public.update_delivery_updated_at_column();

CREATE TRIGGER update_delivery_routes_updated_at
    BEFORE UPDATE ON public.delivery_routes
    FOR EACH ROW
    EXECUTE FUNCTION public.update_delivery_updated_at_column();

CREATE TRIGGER update_route_stops_updated_at
    BEFORE UPDATE ON public.route_stops
    FOR EACH ROW
    EXECUTE FUNCTION public.update_delivery_updated_at_column();