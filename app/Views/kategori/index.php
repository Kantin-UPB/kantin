<table border="1" cellpadding="5">
    <tr>
        <th>ID</th>
        <th>Nama Kategori</th>
    </tr>

    <?php foreach ($kategori as $item): ?>
    <tr>
        <td><?= $item['id_kategori']; ?></td>
        <td><?= $item['nama_kategori']; ?></td>
    </tr>
    <?php endforeach; ?>
</table>