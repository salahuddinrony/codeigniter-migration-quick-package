<?php
/**
 * CodeIgniter Migrate
 *
 * @author  Natan Felles <natanfelles@gmail.com>
 * @link    http://github.com/natanfelles/codeigniter-migrate
 */
defined('BASEPATH') or exit('No direct script access allowed');
/**
 * @var array $assets             Assets links
 * @var bool  $migration_disabled Migration status
 * @var array $migrations         Migration files
 * @var int   $current_version    Current migration version
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Migrate</title>
    <link rel="stylesheet" href="<?= $assets['bootstrap_css'] ?>">
    <style>
        body {
            padding-top: 20px;
        }

        .btn-migrate {
            width: 100%;
        }
    </style>
</head>
<body>
<div class="container">
    
    <div class="row">
        <div class="col-md-9">
            <h1 class="text-center">
                <i class="glyphicon glyphicon-fire"></i> Database Migration<br>
                <small>Best way to manage database tables</small>
            </h1>
        </div>
        <div class="col-md-3">
            <h4 class="text-center0">
                <i class="glyphicon glyphicon-user"></i> <?php echo $this->session->userdata('name'); ?><br>
                <small></small>
            </h4>
            <a href="<?php echo base_url('dashboard'); ?>">
                <i class="glyphicon glyphicon-th"></i> <span>Dashboard</span>
            </a>
            &nbsp;|&nbsp; 
            <a title="Logout" href="<?php echo base_url('logout'); ?>">
                <i class="glyphicon glyphicon-off"></i> <span class="text">Logout</span>
            </a>
            <h2 class="text-center0"><?php echo $companyname; ?></h2>
        </div>
    </row>

	<?php if (isset($migration_disabled)) : ?>
        <div class="alert alert-info">Migration is disabled.</div>
	<?php else : ?>
        <div class="row">
            <div class="col-md-9">
                <div id="msg-migrate">
                    <div class="msg">
                        <div class="alert alert-info">
                            <strong> <i class="glyphicon glyphicon-exclamation-sign"></i> Migration Info :</strong><br> The current migration version is
                            <strong><?= $current_version ?></strong>.
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="well">
                    <div class="btn-group btn-group-justified" role="group">
                        <div class="btn-group" role="group">
                            <input type="checkbox" name="getReset" id="getReset" value="resetTrue" /> Reset Migrations ?
                            <button id="resetMigrate" class="btn btn-danger btn-migrate" data-version="0"
                                    autocomplete="off">
                                    <i class="glyphicon glyphicon-refresh"></i> Reset Migrations
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <table class="table table-striped table-hover table-bordered">
            <thead>
            <tr>
                <th class="text-center">Order</th>
                <th>Version</th>
                <th>File</th>
                <th>Status</th>
                <th class="text-center">Action</th>
            </tr>
            </thead>
            <tbody id="migrations">
			<?php if (empty($migrations)) : ?>
                <tr>
                    <td colspan="4">No migrations.</td>
                </tr>
			<?php else : ?>
				<?php $s = 0; foreach (array_reverse($migrations) as $migration) : $s++ ?>
                    <tr<?= $migration['version'] != $current_version ? '' : ' class="success"' ?>>
                        <th class="text-center"><?= isset($order) ? --$order : $order = count($migrations) ?></th>
                        <td><?= $migration['version'] ?></td>
                        <td><?= substr($migration['file'],11) ?></td>
                        <td>
                            <?php if($order == $current_version || $order <= $current_version){ ?>
                                <span class="label label-success"> <i class="glyphicon glyphicon-ok"></i> Migrated</span>
                            <?php }else{echo '<span class="label label-danger"> <i class="glyphicon glyphicon-remove"></i> Not Yet Migrated</span>'; } ?>
                        </td>
                        <td>
                            <?php if($order == $current_version || $order <= $current_version){echo ' <i class="glyphicon glyphicon-check"></i> Done'; }else if($order == $current_version+1){ ?>
							    <button data-version="<?= $migration['version'] ?>"
                                    class="btn btn-sm btn-primary btn-migrate" autocomplete="off">
                                    <i class="glyphicon glyphicon-play"></i> Run Migrate
                                </button>
                            <?php }else{echo '<b class="text-danger"> <i class="glyphicon glyphicon-dashboard"></i> Waiting</b>'; } ?>
                        </td>
                    </tr>
				<?php endforeach ?>
			<?php endif ?>
            </tbody>
        </table>
	<?php endif ?>
</div>
<script src="<?= $assets['jquery'] ?>"></script>
<script src="<?= $assets['bootstrap_js'] ?>"></script>
<script>
    $(document).ready(function () {
        var btn_migrate = $('.btn-migrate');
        //btn_migrate.prepend('<i class="glyphicon glyphicon-play"></i> ');
        btn_migrate.click(function () {
            var btn = $(this);
            btn.button('loading');
            console.log(btn.data('version'));
            var d = {
                name: 'version',
                value: btn.data('version')
            };
            $.when($.ajax("<?= site_url('migrate/token') ?>", {
                cache: false,
                error: function () {
                    msg('#msg-migrate', 'danger', {content: 'CSRF Token could not be get.'});
                }
            })).done(function (t) {
                console.log(t);
                d = $.merge($.makeArray(d), $.makeArray(t));
                console.log(d);
                $.post("<?= site_url('migrate/post') ?>", d, function (r) {
                    console.log(r);
                    msg('#msg-migrate', r.type, r);
                    if (r.type === 'success') {
                        $('#migrations').children('tr').removeClass('success');
                        btn.parent().parent().addClass('success');
                        location.reload(true);
                    }
                    btn.button('reset');
                }, 'json').fail(function () {
                    msg('#msg-migrate', 'danger', {content: 'Something is wrong.'});
                });
            });
            return false;
        });

        // Migration Reset Button Hide & Show
        $('#resetMigrate').hide();
        $('input[type="checkbox"]').click(function(){
            if($(this).is(':checked')){
                $('#resetMigrate').show();
            }else if($(this).is(':not(:checked)')){
                $('#resetMigrate').hide();
            }
        });

    });

    

    function msg(parent, type, r) {
        var h = '';
        if (r.header) {
            h += '<strong>' + r.header + '</strong><br>';
        }
        // If Response Content is an Object we will list it
        if (typeof r.content === 'object') {
            var o = '<ul>';
            $.each(r.content, function (k, v) {
                o += '<li>' + v + '</li>';
            });
            o += '</ul>';
            h += o;
        } else {
            h += r.content;
        }
        $(parent).children('.msg')
            .removeClass()
            .addClass('msg alert alert-' + type)
            .html(h + '<span class="pull-right">' + new Date().toLocaleTimeString() +'</span>');
    }
</script>
</body>
</html>
