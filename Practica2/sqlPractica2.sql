use ComprasBD
-- Insertar datos de ejemplo para clientes
insert into Clientes (rifcliente, cedula, nombrec, direccionc, telefonoc, statusc, fechaafiliacion, fechadesafiliacion, email)
values
	('v123456789', '1234567890', 'Jose', 'Direccion A', '1234567', 'S', '2023-07-01', null, 'jose@example.com'),
	('v987654321', '9876543210', 'Andres', 'Direccion B', '9876543', 'S', '2023-06-01', null, 'andres@example.com'),
	('v543216789', '5432167890', 'Roberth', 'Direccion C', '5432167', 'S', '2023-03-01', '2024-03-01', 'roberth@example.com'),
	('v987612345', '9876123450', 'Gabriel', 'Direccion D', '9876123', 'A', '2023-04-01', '2024-04-01', 'gabriel@example.com');

-- Insertar datos de ejemplo para facturas
insert into Facturas (numfactura, rifcliente, fechaemision, tipopago, tipomoneda)
values
	('f2023001', 'v123456789', '2023-01-15', 'E', 'Bolivares'),
	('f2023002', 'v123456789', '2023-01-20', 'E', 'Bolivares'),
	('f2023003', 'v987654321', '2023-02-10', 'T', 'Divisas'),
	('f2023004', 'v543216789', '2023-03-05', 'E', 'Bolivares'),
	('f2023005', 'v987612345', '2023-04-25', 'T', 'Petro');

-- Insertar datos de ejemplo para líneas
insert into Lineas (codlinea, descripcionl)
values
	('l001', 'Linea 1 Descripcion'),
	('l002', 'Linea 2 Descripcion'),
	('l003', 'Linea 3 Descripcion'),
('l004', 'Artículos de Papelería');

-- Insertar datos de ejemplo para artículos
insert into Articulos (codarticulo, descripcion, codlinea, precio, existencia, maximo, minimo, statusa)
values
	('a001', 'Articulo 1', 'l001', 100.00, 50, 100, 10, 'A'),
	('a002', 'Articulo 2', 'l002', 150.00, 30, 80, 5, 'D'),
	('a003', 'Articulo 3', 'l001', 75.00, 70, 120, 15, 'R'),
	('a004', 'Articulo 4', 'l003', 200.00, 20, 50, 2, 'A'),
	('a005', 'Articulo 5', 'l002', 120.00, 40, 60, 10, 'D'),
	('a006', 'Tijera', 'l004', 1000, 3, 5, 1, 'A');

-- Insertar datos de ejemplo para detalles facturas
insert into DetallesFacturas (numfactura, codarticulo, cantidad, precio)
values
	('f2023001', 'a001', 2, 100.00),
	('f2023002', 'a002', 1, 150.00),
	('f2023003', 'a003', 3, 75.00),
	('f2023004', 'a004', 1, 200.00),
	('f2023005', 'a005', 2, 120.00),
	('f2023005', 'a006', 50001, 1000.00)

-- Insertar datos de ejemplo para proveedores
insert into Proveedores (codproveedor, razonsocial, direccionp, telefonop, statusp)
values
	('p001', 'Proveedor 1', 'Direccion Proveedor 1', '1234567', 'A'),
	('p002', 'Proveedor 2', 'Direccion Proveedor 2', '9876543', 'S'),
	('p003', 'Proveedor 3', 'Direccion Proveedor 3', '5432167', 'E');

-- Insertar datos de ejemplo para compras artículos
insert into ComprasArticulos (codproveedor, codarticulo, fechacompra, cantidad, precio)
values
	('p001', 'a001', '2023-01-10', 5, 90.00),
	('p002', 'a002', '2023-02-20', 3, 140.00),
	('p003', 'a003', '2023-03-15', 2, 70.00),
	('p001', 'a004', '2023-04-05', 4, 180.00),
	('p002', 'a005', '2023-05-01', 1, 110.00);

SELECT RIFCliente, NombreC, DireccionC, FechaAfiliacion
FROM Clientes
WHERE YEAR(FechaAfiliacion) = YEAR(GETDATE()) - 1
AND FechaDesafiliacion <= GETDATE()
ORDER BY FechaAfiliacion ASC, NombreC ASC;

SELECT DISTINCT c.RIFCliente AS NumCliente, c.Cedula, c.NombreC
FROM Clientes c, Facturas f
WHERE c.RIFCliente = f.RIFCliente
AND f.FechaEmision BETWEEN '2023-01-01' AND '2023-12-31'
ORDER BY c.Cedula DESC;

SELECT l.CodLinea, l.DescripcionL, COUNT(a.CodArticulo) AS CantidadArticulos, AVG(a.Precio) AS PrecioPromedio
FROM Lineas l, Articulos a
WHERE l.CodLinea = a.CodLinea
GROUP BY l.CodLinea, l.DescripcionL
ORDER BY COUNT(a.CodArticulo) DESC;

SELECT f.NumFactura, f.RIFCliente, f.FechaEmision
FROM Facturas f, DetallesFacturas df, Articulos a, Lineas l
WHERE f.NumFactura = df.NumFactura
AND df.CodArticulo = a.CodArticulo
AND a.CodLinea = l.CodLinea
AND l.DescripcionL = 'Artículos de Papelería'
AND YEAR(f.FechaEmision) = YEAR(GETDATE()) - 1
ORDER BY f.RIFCliente, f.NumFactura;

SELECT a.CodArticulo, a.Descripcion AS Nombre, SUM(df.Cantidad * df.Precio) AS TotalVentas
FROM Articulos a, DetallesFacturas df, Facturas f
WHERE a.CodArticulo = df.CodArticulo
AND df.NumFactura = f.NumFactura
AND YEAR(f.FechaEmision) = YEAR(GETDATE()) - 1
GROUP BY a.CodArticulo, a.Descripcion
HAVING SUM(df.Cantidad * df.Precio) > 50000000
ORDER BY TotalVentas DESC;

SELECT p.CodProveedor, p.RazonSocial AS NombreProveedor,
   	a.CodArticulo, a.Descripcion AS NombreArticulo,
   	ca.FechaCompra, ca.Precio, ca.Cantidad
FROM Proveedores p, ComprasArticulos ca, Articulos a
WHERE p.CodProveedor = ca.CodProveedor
AND ca.CodArticulo = a.CodArticulo
AND ca.Precio = (SELECT MIN(Precio) FROM ComprasArticulos WHERE CodArticulo = 'a001')
ORDER BY ca.Precio;

-- Valor de ejemplo
UPDATE Articulos SET Existencia = 0 WHERE CodArticulo = 'a001'
SELECT * FROM Articulos
-- Ejecutar actualización
UPDATE Articulos
SET StatusA = 'D'
WHERE Existencia = 0
AND CodArticulo IN (
	SELECT a.CodArticulo
	FROM Articulos a, DetallesFacturas df, Facturas f
    WHERE a.CodArticulo = df.CodArticulo
    AND df.NumFactura = f.NumFactura
    AND f.FechaEmision < DATEADD(YEAR, -1, GETDATE())
);
SELECT * FROM Articulos

CREATE TABLE HistoricoProveedores (
	CodProveedor varchar(10) primary key,
	RazonSocial varchar(300) not null,
	DireccionP varchar(300) not null,
	TelefonoP varchar(12),
	StatusP char(1) not null check(StatusP='E')
);

INSERT INTO HistoricoProveedores (CodProveedor, RazonSocial, DireccionP, TelefonoP, StatusP)
SELECT CodProveedor, RazonSocial, DireccionP, TelefonoP, StatusP
FROM Proveedores
WHERE StatusP = 'E';

DELETE FROM ComprasArticulos
WHERE CodProveedor IN (
	SELECT CodProveedor
	FROM Proveedores
	WHERE StatusP = 'E'
);

DELETE FROM Proveedores
WHERE StatusP = 'E';

-- La salida a archivo histórico se puede hacer con la utilidad bcp, ejecutándose con permisos adecuados en la base de datos: bcp [ComprasBD].[dbo].[HistoricoProveedores] out "c:\ejemplo.txt" -T -c