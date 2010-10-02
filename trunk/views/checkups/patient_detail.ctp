<?php if (!empty($patient)):?>
<table class="border">
    <tr>
        <td class="label">Nama</td>
        <td class="val">
        <?php echo $patient['Patient']['name'];?>
        </td>
    </tr>
    <tr>
        <td class="label">No. Pasien</td>
        <td class="val">
        <?php echo $patient['Patient']['code'];?>
        </td>
    </tr>
    <tr>
        <td class="label">Tipe Pasien</td>
        <td class="val">
        <?php echo $patient['PatientType']['name'];?>
        </td>
    </tr>
    <tr>
        <td class="label">Tgl. Lahir</td>
        <td class="val">
        <?php echo $time->format('d/m/Y', $patient['Patient']['dob']);?>
        </td>
    </tr>
    <tr>
        <td class="label">Jenis Kelamin</td>
        <td class="val">
        <?php echo $patient['Patient']['sex'] == 'M' ? 'Laki-laki' : 'Perempuan';?>
        </td>
    </tr>
</table>
<?php endif;?>
