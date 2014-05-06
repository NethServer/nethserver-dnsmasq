.. --initial-header-level=2

DHCP
====

El DHCP (Dynamic Host Configuration Protocol) permite aginar direcciones IP a los clientes de la red local.

Configurar
----------

Configurar el servidor DHCP.

Deshabilitado
    El servidor DHCP se desactivará y los clientes de LAN no lo recibirán la dirección de forma automática por este servidor. Selecciona esta opción si hay otro servidor DHCP en su red local.

Habilitado
    El  servidor emitirá direcciones IP a los equipos en la red local (recomendado).

Comienzo
    La primera dirección IP del rango asignado a los clientes en la LAN.

Fin
    La última dirección IP del rango, de las direcciones entre INICIO y FINAL que serán asignadas a los clientes.

Crear / Modificar
-----------------

Agrega una nueva asignación estática (reserva) para el servidor DHCP. El dispositivo con la dirección MAC especificada siempre recibirá la dirección IP especificada. 

Nombre del Host
    El nombre de host que desea asignar a los clientes en la LAN con la dirección IP especificada.

Descripción
    Una descripción opcional para identificar el sistema.

Dirección IP
    La dirección IP que desea asignar.

Dirección MAC
    La dirección MAC del sistema de red (por ejemplo, 11:22:33:44:55:66:77:88).
