--use ComprasBD

/*
insert into Lineas (CodLinea, DescripcionL) values
	('l005', 'Electrodomesticos')
insert into Articulos (CodArticulo, Descripcion, CodLinea, Precio, Existencia, Maximo, Minimo, StatusA) values
	('a007', 'Lavadora', 'l005', 320.10, 5, 20, 0, 'A'),
	('a008', 'Secadora', 'l005', 255.25, 2, 10, 2, 'R')
insert into Facturas (NumFactura, RIFCliente, FechaEmision, TipoPago, TipoMoneda) values
	('f2024001', 'v543216789', '2024-01-15', 'T', 'Bolivares')
insert into DetallesFacturas (NumFactura, CodArticulo, Cantidad, Precio) values
	('f2024001', 'a007', 1, 320.10),
	('f2024001', 'a008', 2, 510.5)
*/

/*
create view listado_pagos_clientes
as select c.NombreC, c.DireccionC, c.TelefonoC, l.DescripcionL, df.Precio
from Clientes c, Lineas l, Articulos a, Facturas f, DetallesFacturas df
where f.RIFCliente = c.RIFCliente
and f.NumFactura = df.NumFactura
and a.CodArticulo = df.CodArticulo
and l.CodLinea = a.CodLinea
*/

/*
create view listado_pedidos_proveedores
as select p.CodProveedor, p.RazonSocial, p.TelefonoP, ca.FechaCompra, SUM(ca.Cantidad * ca.Precio) as cantidad_total
from Proveedores p, ComprasArticulos ca
where p.CodProveedor = ca.CodProveedor
group by p.CodProveedor, p.RazonSocial, p.TelefonoP, ca.FechaCompra
*/

select * from listado_pagos_clientes
where DescripcionL = 'Electrodomesticos'