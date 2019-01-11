<?php
/**
 * @author      : wukun<charmfocus@gmail.com>
 * @copyright(c): 14/11/17
 * @version     : $id$
 */

if (!empty($msg) && is_array($msg)) {
    foreach ($msg as &$_row) {
        if (is_array($_row)) {
            $_row = join(',', $_row);
        }
    }

    $msg = '<ul><li>' . join('</li><li>', $msg) . '</li></ul>';
}
echo '<blockquote class="layui-container layui-elem-quote quote-' . $status . '">' . $msg . '</blockquote>';

?>

<?php
if ($timeout > 0) {
    ?>
    <script>
        window.setTimeout(
            function () {
                window.location.href = '<?=$callbackUrl?>';
            }, <?=$timeout * 1000?>);

    </script>
    <?php
}
?>


