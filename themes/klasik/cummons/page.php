<?php if ($paging->num_rows > $paging->per_page): ?>
    <div class="box-footer  text-center">
        <div>Halaman <?= $paging->page ?> dari <?= $paging->end_link ?></div>
        <ul class="pagination pagination-sm no-margin">
            <?php if ($paging->start_link): ?>
                <li><a href="<?= site_url("$paging_page/$paging->start_link")?>" title="Halaman Pertama"><i class="fa fa-fast-backward"></i>&nbsp;</a></li>
            <?php endif; ?>
            <?php if ($paging->prev): ?>
                <li><a href="<?= site_url("$paging_page/$paging->prev")?>" title="Halaman Sebelumnya"><i class="fa fa-backward"></i>&nbsp;</a></li>
            <?php endif; ?>
            <?php for ($i=$paging->start_link; $i<=$paging->end_link; $i++): ?>
                <li class="<?php ($paging->page != $i) or print('active');?>"><a href="<?=site_url("$paging_page/$i")?>" title="<?= 'Halaman '.$i ?>"><?= $i ?></a></li>
            <?php endfor; ?>
            <?php if ($paging->next): ?>
                <li><a href="<?= site_url("$paging_page/$paging->next")?>" title="Halaman Selanjutnya"><i class="fa fa-forward"></i>&nbsp;</a></li>
            <?php endif; ?>
            <?php if ($paging->end_link): ?>
                <li><a href="<?= site_url("$paging_page/$paging->end_link")?>" title="Halaman Terakhir"><i class="fa fa-fast-forward"></i>&nbsp;</a></li>
            <?php endif; ?>
        </ul>
    </div>
<?php endif; ?>