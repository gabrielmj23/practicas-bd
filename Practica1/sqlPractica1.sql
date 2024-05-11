--create database ComprasBD
--use ComprasBD

/*
create table Clientes (
	RIFCliente varchar(10) primary key,
	Cedula varchar(10) not null unique,
	NombreC varchar(40) not null,
	DireccionC varchar(300) not null,
	TelefonoC varchar(12),
	FechaNac date not null,
	StatusC char(1) not null check(StatusC='A' or StatusC='S' or StatusC='M'),
	FechaAfiliacion datetime not null,
	FechaDesafiliacion datetime,
	constraint chequear_fechas check(FechaDesafiliacion > FechaAfiliacion)
)
*/
/*
create table Facturas (
	NumFactura varchar(10) primary key,
	RIFCliente varchar(10) not null foreign key (RIFCliente) references Clientes(RIFCliente) on update cascade,
	FechaEmision datetime not null,
	TipoPago char(1) not null check(TipoPago='E' or TipoPago='T'),
	TipoMoneda char(1) not null check(TipoMoneda='B' or TipoMoneda='D' or TipoMoneda='P')
)
*/
/*
create table Lineas (
	CodLinea varchar(10) primary key,
	DescripcionL varchar(300) not null unique
)
*/
/*
create table Articulos (
	CodArticulo varchar(10) primary key,
	Descripcion varchar(300) not null,
	CodLinea varchar(10) not null foreign key (CodLinea) references Lineas(CodLinea) on update cascade,
	Precio decimal(8,2) not null check(Precio <> 0),
	Existencia int not null,
	Maximo int not null,
	Minimo int not null,
	StatusA char(1) not null check(StatusA='A' or StatusA='D' or StatusA='R')
)
*/
/*
create table DetallesFacturas (
	NumFactura varchar(10) not null,
	CodArticulo varchar(10) not null,
	primary key(NumFactura, CodArticulo),
	foreign key (NumFactura) references Facturas(NumFactura) on update cascade,
	foreign key (CodArticulo) references Articulos(CodArticulo) on update cascade,
	Cantidad int not null check(Cantidad <> 0),
	Precio decimal(8,2) not null check(Precio <> 0)
)
*/
/*
create table Proveedores (
	CodProveedor varchar(10) primary key,
	RazonSocial varchar(300) unique not null,
	DireccionP varchar(300) not null,
	TelefonoP varchar(12),
	StatusP char(1) not null check(StatusP='A' or StatusP='S' or StatusP='E')
)
*/
/*
create table ComprasArticulos (
	CodProveedor varchar(10) not null,
	CodArticulo varchar(10) not null,
	FechaCompra datetime not null,
	primary key(CodProveedor, CodArticulo, FechaCompra),
	foreign key (CodProveedor) references Proveedores(CodProveedor) on update cascade,
	foreign key (CodArticulo) references Articulos(CodArticulo) on update cascade,
	Cantidad int not null check(Cantidad <> 0),
	Precio decimal(8, 2) not null check(Precio <> 0)
)
*/
--create index indice_linea on Articulos (CodLinea)
/*
alter table Facturas alter column TipoMoneda varchar(10) not null;
alter table Facturas drop constraint CK__Facturas__TipoMo__4316F928;
alter table Facturas add constraint Valor_Tipo_Moneda check(TipoMoneda='Bolivares' or TipoMoneda='Divisas' or TipoMoneda='Petro');
*/
--alter table Articulos add FechaDesincorporacion datetime;
/*
alter table Clientes drop column FechaNac;
alter table Clientes add email varchar(20) not null;
*/
--alter table Articulos add constraint Validar_Min_Max check(Minimo <= Maximo)
