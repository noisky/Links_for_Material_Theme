<?php
include 'header.php';
include 'menu.php';
?>


<div class="main">
    <div class="body container">
        <?php include 'page-title.php'; ?>
        <div class="row typecho-page-main manage-metas">
                <?php $tab = 'settings' === $request->get('tab') ? 'settings' : 'links'; ?>
                <div class="col-mb-12">
                    <ul class="typecho-option-tabs clearfix">
						<li class="<?php echo 'links' === $tab ? 'current' : ''; ?>"><a href="<?php $options->adminUrl('extending.php?panel=Links%2Fmanage-links.php'); ?>"><?php _e('友情链接'); ?></a></li>
						<li class="<?php echo 'settings' === $tab ? 'current' : ''; ?>"><a href="<?php $options->adminUrl('extending.php?panel=Links%2Fmanage-links.php&tab=settings'); ?>"><?php _e('设置'); ?></a></li>
                        <li><a href="https://github.com/noisky/Links_for_Material_Theme" title="查看友情链接使用帮助" target="_blank"><?php _e('帮助'); ?></a></li>
                    </ul>
                </div>

                <?php if ('settings' === $tab): ?>
                <div class="col-mb-12 col-tb-8" role="form">
                    <?php $outputMode = Links_Plugin::getOutputMode(); ?>
                    <h3><?php _e('显示设置'); ?></h3>
                    <form method="post" action="<?php $security->index('/action/links-edit?do=save-settings'); ?>">
                        <ul class="typecho-option">
                            <li>
                                <label class="typecho-label"><?php _e('输出排序设置'); ?></label>
                                <p>
                                    <label>
                                        <input type="radio" name="outputMode" value="order"<?php if ('order' === $outputMode) echo ' checked'; ?> />
                                        <?php _e('按照后台排序'); ?>
                                    </label>
                                    <label>
                                        <input type="radio" name="outputMode" value="daily"<?php if ('daily' === $outputMode) echo ' checked'; ?> />
                                        <?php _e('每日随机输出'); ?>
                                    </label>
                                    <label>
                                        <input type="radio" name="outputMode" value="request"<?php if ('request' === $outputMode) echo ' checked'; ?> />
                                        <?php _e('每次随机输出'); ?>
                                    </label>
                                </p>
                                <p class="description">
                                    <?php _e('按照后台排序：使用默认拖拽顺序'); ?><br>
                                    <?php _e('每日随机输出：在当天保持顺序不变'); ?><br>
                                    <?php _e('每次随机输出：会在每次页面生成时重新打乱顺序'); ?>
                                </p>
                            </li>
                        </ul>
                        <ul class="typecho-option typecho-option-submit">
                            <li>
                                <button type="submit" class="btn primary"><?php _e('保存设置'); ?></button>
                            </li>
                        </ul>
                    </form>
                </div>
                <?php else: ?>
                <div class="col-mb-12 col-tb-8" role="main">                  
                    <?php
						$prefix = $db->getPrefix();
						$links = $db->fetchAll($db->select()->from($prefix.'links')->order($prefix.'links.order', Typecho_Db::SORT_ASC));
                    ?>
                    <form method="post" name="manage_categories" class="operate-form">
                    <div class="typecho-list-operate clearfix">
                        <div class="operate">
                            <label><i class="sr-only"><?php _e('全选'); ?></i><input type="checkbox" class="typecho-table-select-all" /></label>
                            <div class="btn-group btn-drop">
                                <button class="btn dropdown-toggle btn-s" type="button"><i class="sr-only"><?php _e('操作'); ?></i><?php _e('选中项'); ?> <i class="i-caret-down"></i></button>
                                <ul class="dropdown-menu">
                                    <li><a lang="<?php _e('你确认要删除这些链接吗?'); ?>" href="<?php $options->index('/action/links-edit?do=delete'); ?>"><?php _e('删除'); ?></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div id="links-table-wrap" class="typecho-table-wrap">
                        <table id="links-table" class="typecho-list-table">
                            <colgroup>
                                <col width="54"/>
                                <col width="20"/>
								<col width="25%"/>
								<col width=""/>
								<col width="15%"/>
								<col width="10%"/>
                            </colgroup>
                            <thead>
                                <tr>
                                    <th class="links-sort-column" title="<?php _e('拖动整行调整顺序'); ?>"><?php _e('排序'); ?></th>
                                    <th> </th>
									<th><?php _e('链接名称'); ?></th>
									<th><?php _e('链接地址'); ?></th>
									<th><?php _e('分类'); ?></th>
									<th><?php _e('图片'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
								<?php if(!empty($links)): $alt = 0;?>
								<?php foreach ($links as $link): ?>
                                <tr id="lid-<?php echo $link['lid']; ?>" title="<?php _e('拖动整行调整顺序'); ?>">
                                    <td class="links-sort-indicator" title="<?php _e('拖动整行调整顺序'); ?>" aria-label="<?php _e('拖动整行调整顺序'); ?>">⋮⋮</td>
                                    <td><input type="checkbox" value="<?php echo $link['lid']; ?>" name="lid[]"/></td>
									<td><a href="<?php echo $request->makeUriByRequest('lid=' . $link['lid']); ?>" title="点击编辑"><?php echo $link['name']; ?></a>
									<td><?php echo $link['url']; ?></td>
									<td><?php echo $link['sort']; ?></td>
									<td><?php
										if ($link['image']) {
											echo '<a href="'.$link['image'].'" title="点击放大" target="_blank"><img class="avatar" src="'.$link['image'].'" alt="'.$link['name'].'" width="32" height="32"/></a>';
										} else {
											$options = Typecho_Widget::widget('Widget_Options');
											$nopic_url = Typecho_Common::url('/usr/plugins/Links/nopic.png', $options->siteUrl);
											echo '<img class="avatar" src="'.$nopic_url.'" alt="NOPIC" width="32" height="32"/>';
										}
									?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php else: ?>
                                <tr>
                                    <td colspan="6"><h6 class="typecho-list-table-title"><?php _e('没有任何链接'); ?></h6></td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <div id="links-sort-loading" class="links-sort-loading" role="status" aria-live="polite" aria-hidden="true">
                            <span class="links-sort-loading-spinner" aria-hidden="true"></span><?php _e('正在保存排序…'); ?>
                        </div>
                    </div>
                    </form>
                </div>
                <div class="col-mb-12 col-tb-4" role="form">
                    <?php Links_Plugin::form()->render(); ?>
                </div>
                <?php endif; ?>
        </div>
    </div>
</div>

<?php
include 'copyright.php';
include 'common-js.php';
?>

<style type="text/css">
.links-sort-popup {
    position: fixed;
    top: 36px;
    left: 0;
    box-sizing: border-box;
    z-index: 101;
}

#links-table-wrap {
    position: relative;
}

#links-table-wrap.links-sort-saving::after {
    content: '';
    position: absolute;
    z-index: 2;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    background: rgba(246, 246, 243, .65);
    cursor: wait;
}

#links-table-wrap.links-sort-saving #links-table {
    opacity: .65;
}

.links-sort-loading {
    display: none;
    position: absolute;
    z-index: 3;
    top: 50%;
    left: 50%;
    align-items: center;
    padding: 8px 14px;
    background: #fff;
    color: #467b96;
    box-shadow: 0 2px 8px rgba(0, 0, 0, .12);
    transform: translate(-50%, -50%);
    white-space: nowrap;
}

#links-table-wrap.links-sort-saving .links-sort-loading {
    display: flex;
}

.links-sort-loading-spinner {
    display: inline-block;
    width: 14px;
    height: 14px;
    margin-right: 8px;
    border: 2px solid #c9dbe5;
    border-top-color: #467b96;
    border-radius: 50%;
    animation: links-sort-loading-spin .8s linear infinite;
}

@keyframes links-sort-loading-spin {
    to {
        transform: rotate(360deg);
    }
}

#links-table tbody tr {
    cursor: grab;
}

#links-table tbody tr:active {
    cursor: grabbing;
}

#links-table tbody tr:hover {
    background-color: #f5faff;
}

#links-table tbody tr.links-row-dragging {
    cursor: grabbing;
    background-color: #e8f3ff;
    box-shadow: 0 2px 8px rgba(0, 0, 0, .12);
}

#links-table .links-sort-column,
#links-table .links-sort-indicator {
    width: 54px;
    padding-left: 6px;
    padding-right: 6px;
    text-align: center;
}

#links-table .links-sort-column {
    white-space: nowrap;
}

#links-table .links-sort-indicator {
    color: #aeb8c2;
    font-size: 18px;
    line-height: 1;
    user-select: none;
}

#links-table tbody tr:hover .links-sort-indicator {
    color: #467b96;
}

</style>

<?php if ('links' === $tab): ?>
<script type="text/javascript">
(function () {
    $(document).ready(function () {
        var sortSuccessMessage = <?php echo json_encode(_t('友情链接排序已保存')); ?>,
            sortErrorMessage = <?php echo json_encode(_t('排序保存失败，请重试')); ?>,
            pendingSortRequest = null;

        function clearSortNotice() {
            $('.links-sort-popup').stop(true, true).remove();
        }

        function showSortNotice(message, type) {
            var head = $('.typecho-head-nav'),
                notice = $('<div class="message popup ' + type + ' links-sort-popup"><ul><li></li></ul></div>');

            clearSortNotice();
            notice.find('li').text(message);
            if (head.length > 0) {
                notice.insertAfter(head);
            } else {
                notice.prependTo(document.body);
            }

            notice.slideDown(function () {
                var current = $(this),
                    color = '#C6D880';

                if (current.hasClass('error')) {
                    color = '#FBC2C4';
                } else if (current.hasClass('notice')) {
                    color = '#FFD324';
                }

                current.effect('highlight', {color : color})
                    .delay(5000).fadeOut(function () {
                    $(this).remove();
                });
            });
        }

        function setSortSaving(saving) {
            var wrap = $('#links-table-wrap');

            wrap.toggleClass('links-sort-saving', saving)
                .attr('aria-busy', saving ? 'true' : 'false');
            $('#links-sort-loading').attr('aria-hidden', saving ? 'false' : 'true');
        }

        function saveSort(ids) {
            setSortSaving(true);

            var request = $.post('<?php $options->index('/action/links-edit?do=sort'); ?>',
                $.param({lid : ids}))
                .done(function () {
                    showSortNotice(sortSuccessMessage, 'success');
                })
                .fail(function () {
                    showSortNotice(sortErrorMessage, 'error');
                })
                .always(function () {
                    if (pendingSortRequest === request) {
                        pendingSortRequest = null;
                        setSortSaving(false);
                    }
                });

            pendingSortRequest = request;
        }

        var table = $('#links-table').tableDnD({
            onDragClass : 'links-row-dragging',
            onDragStart : function () {
                clearSortNotice();
            },
            onDrop : function () {
                var ids = [];

                $('input[type=checkbox]', table).each(function () {
                    ids.push($(this).val());
                });

                if (!pendingSortRequest) {
                    saveSort(ids);
                }

                $('tr', table).each(function (i) {
                    if (i % 2) {
                        $(this).addClass('even');
                    } else {
                        $(this).removeClass('even');
                    }
                });
            }
        });

        table.tableSelectable({
            checkEl     :   'input[type=checkbox]',
            rowEl       :   'tr',
            selectAllEl :   '.typecho-table-select-all',
            actionEl    :   '.dropdown-menu a'
        });

        $('.btn-drop').dropdownMenu({
            btnEl       :   '.dropdown-toggle',
            menuEl      :   '.dropdown-menu'
        });

        $('.dropdown-menu button.merge').click(function () {
            var btn = $(this);
            btn.parents('form').attr('action', btn.attr('rel')).submit();
        });

        <?php if (isset($request->lid)): ?>
        $('.typecho-mini-panel').effect('highlight', '#AACB36');
        <?php endif; ?>
    });
})();
</script>
<?php endif; ?>
<?php include 'footer.php'; ?>
