<table border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td align="center" height="30">
            <font size="15"><b>COMPROBANTE DE ALTA</b></font>
        </td>    
    </tr>

</table>

<br>



<br>

<table border="0" cellpadding="2" cellspacing="0" style="border: 1px solid #cccccc">
    <tr>
        <td>
            <table border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="30%"><strong>Folio</strong> </td>
                    <td width="70%">: <?= $folio; ?> </td>    
                </tr>
                <tr>
                    <td><strong>Fecha Carga</strong> </td>
                    <td>: <?= $fecha_carga; ?></td>    
                </tr>
                <tr>
                    <td><strong>Usuario Cargador</strong> </td>
                    <td>: <?= $usuario_cargador; ?> </td>    
                </tr>
                <tr>
                    <td><strong>Comentario</strong> </td>
                    <td>: <?= $comentario; ?> </td>    
                </tr>
            </table>
        </td>    
    </tr>
</table>


<br></br>
<br></br>

<table width="100%" border="1" cellpadding="2" cellspacing="0">
    <tr>
        <td rowspan="2"><div align="center"><strong>C&oacute;digo inventario </strong></div></td>
        <td colspan="6" bgcolor="#cccccc"><div align="center"><strong>Bien de Registro</strong></div></td>
        <td rowspan="2"><div align="center"><strong>Nro. Serie </strong></div></td>
        <td rowspan="2"><div align="center"><strong>Nro. Factura </strong></div></td>
    </tr>


    <tr>
        <td><div align="center"><strong>Nombre</strong></div></td>
        <td><div align="center"><strong>Tipo</strong></div></td>
        <td><div align="center"><strong>Marca</strong></div></td>
        <td><div align="center"><strong>Condici&oacute;n</strong></div></td>
        <td><div align="center"><strong>Descripci&oacute;n</strong></div></td>
        <td><div align="center" ><strong>Ubicaci&oacute;n </strong></div></td>
    </tr>
    <?php foreach ($br_list as $asset_br): ?>
        <tr>
            <td><?php echo $asset_br['asset_num_serie_intern']; ?></td>
            <td><?php echo $asset_br['asset_name']; ?></td>
            <td><?php echo $asset_br['asset_type_name']; ?></td>
            <td><?php echo $asset_br['brand_name']; ?></td>
            <td><?php echo $asset_br['condition_name']; ?></td>
            <td><?php echo $asset_br['asset_description']; ?></td>
            <td><?php echo $asset_br['asset_path_3_niveles']; ?></td>
            <td><?php echo $asset_br['asset_num_serie']; ?></td>
            <td><?php echo $asset_br['asset_num_factura']; ?></td>
            
        </tr>
    <?php endforeach; ?>
</table>








