<?php
define("DATA_FILE", __DIR__ . DIRECTORY_SEPARATOR . 'data.bin');

$action = $_GET['action'] ?? false;

if ($action == 'add') {

    $name = $_POST['name'] ?? false;
    $url = $_POST['url'] ?? false;
    $frequency = $_POST['frequency'] ?? false;
    $start_at = $_POST['start_at'] ?? false;
    $end_at = $_POST['end_at'] ?? false;
    if (empty($name) || empty($url) || empty($frequency) || empty($start_at) || empty($end_at)) {
        show_notice('参数不合法');
    }
    if ($frequency > 1) {
        $frequency = 1;
    }
    if ($frequency < 0.01) {
        $frequency = 0.01;
    }
    insert($name, $url, $frequency, $start_at, $end_at);

} elseif ($action == 'op') {
    if (isset($_POST['edit'])) { /* edit */
        if ($_POST['frequency'] > 1) {
            $_POST['frequency'] = 1;
        }
        if ($_POST['frequency'] < 0.01) {
            $_POST['frequency'] = 0.01;
        }
        update($_POST['id'], $_POST['name'], $_POST['url'], $_POST['frequency'], $_POST['start_at'], $_POST['end_at']);
    } elseif (isset($_POST['delete'])) { /* delete */
        delete($_POST['id']);
    } else {
        show_notice('无效的参数');
    }
}

function show_notice($msg, $url = 'index.php')
{
    header('content-type:text/html;charset=utf-8');
    echo sprintf('<script language="javascript">alert("%s");window.location.href="%s";</script>', $msg, $url);
    exit();
}

function get_records()
{
    return ($data = file_exists(DATA_FILE) ?
        unserialize(file_get_contents(DATA_FILE)) : []) === false ? [] : $data;
}

function save_records($records)
{
    file_put_contents(DATA_FILE, serialize($records));
}

function insert($name, $url, $frequency, $start_at, $end_at)
{
    $records = get_records();
    $id = 0;
    if (empty($records)) {
        $id = 1;
    } else {
        $id = end($records)['id'] + 1;
    }
    $records[] = compact('id', 'name', 'url', 'frequency', 'start_at', 'end_at');
    save_records($records);
    show_notice('添加成功');
}

function update($id, $name, $url, $frequency, $start_at, $end_at)
{
    $records = get_records();
    foreach ($records as $key => $record) {
        if ($id == $record['id']) {
            $records[$key] = compact('id', 'name', 'url', 'frequency', 'start_at', 'end_at');
            break;
        }
    }
    save_records($records);
    show_notice('编辑成功');
}

function delete($id)
{
    $records = get_records();
    foreach ($records as $key => $record) {
        if ($id == $record['id']) {
            unset($records[$key]);
            break;
        }
    }
    save_records($records);
    show_notice('删除成功');
}

?><!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html;charset=utf-8">
<title>xhprof性能分析管理</title>
<script src="http://libs.baidu.com/jquery/1.9.1/jquery.min.js"></script>
<script src="xhprof_html/js/My97DatePicker/WdatePicker.js"></script>
<style type="text/css">
td,input{padding:0;margin:0}
.tg  {border-collapse:collapse;border-spacing:0;}
.tg td{font-family:Arial, sans-serif;font-size:14px;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:#6495ed}
.tg th{font-family:Arial, sans-serif;font-size:14px;font-weight:normal;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;}
.tg .tg-baqh{text-align:center;vertical-align:top; border-color:#6495ed; background-color:#6495ed}
.tg .tg-yw4l{vertical-align:top; text-align:center}
</style>
<script language="javascript">
var get_row_field = function(parent) {
    return {
        name      : $(parent).find('[name="name"]').val();
        url       : $(parent).find('[name="url"]').val(),
        frequency : $(parent).find('[name="frequency"]').val(),
        start_at  : $(parent).find('[name="start_at"]').val(),
        end_at    : $(parent).find('[name="end_at"]').val()
    };
}

$(document).ready(function() {
    $("#add_form").submit(function(){
        var row = get_row_field($(this));
        if (row.name == '' || row.url == '' || row.frequency == '' || row.start_at == '' || row.end_at == '') {
            alert('各字段均不能为空');
            return false;
        }
    });
});
</script>
</head>
<body>
<br />
<form action="?action=add" method="post" id="add_form">
<table class="tg" align="center" width="1000">
  <tr>
    <th class="tg-baqh" colspan="6">添加</th>
  </tr>
  <tr>
    <td class="tg-yw4l">名称</td>
    <td class="tg-yw4l">URL请求地址</td>
    <td class="tg-yw4l">采样频率</td>
    <td class="tg-yw4l">采样起始时间</td>
    <td class="tg-yw4l">采样结束时间</td>
    <td class="tg-yw4l">操作</td>
  </tr>
  <tr>
    <td class="tg-yw4l"><input type="text" size="10" name="name" value=""></td>
    <td class="tg-yw4l"><input type="text" size="40" name="url" value=""></td>
    <td class="tg-yw4l"><input type="text" size="5" name="frequency" value=""></td>
    <td class="tg-yw4l"><input type="text" size="20" name="start_at" value="" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"></td>
    <td class="tg-yw4l"><input type="text" size="20" name="end_at" value="" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"></td>
    <td class="tg-yw4l"><input type="submit" value="submit"</td>
  </tr>
</table>
</form>
<br />
<br />
<table class="tg" align="center" width="1000">
  <tr>
    <th class="tg-baqh" colspan="6">列表/管理</th>
  </tr>
  <tr>
    <td class="tg-yw4l">名称</td>
    <td class="tg-yw4l">URL请求地址</td>
    <td class="tg-yw4l">采样频率</td>
    <td class="tg-yw4l">采样起始时间</td>
    <td class="tg-yw4l">采样结束时间</td>
    <td class="tg-yw4l">操作</td>
  </tr>
<?php
foreach (get_records() as $row) {
?>
<form action="?action=op" method="post" id="op_form">
  <tr>
  <td class="tg-yw4l"><input type="text" size="10" name="name" value="<?php echo $row['name']; ?>"></td>
  <td class="tg-yw4l"><input type="text" size="40" name="url" value="<?php echo $row['url']; ?>"></td>
    <td class="tg-yw4l"><input type="text" size="5" name="frequency" value="<?php echo $row['frequency']; ?>"></td>
    <td class="tg-yw4l"><input type="text" size="20" name="start_at" value="<?php echo $row['start_at']; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"></td>
    <td class="tg-yw4l"><input type="text" size="20" name="end_at" value="<?php echo $row['end_at']; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"></td>
    <td class="tg-yw4l">
        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
        <input type="submit" value="编辑" name="edit">
        <input type="submit" value="删除" name="delete" onclick="return confirm('确定要删除吗？')">
    </td>
  </tr>
</form>
<?php
}
?>
</table>
<br/ >
<br/ >
<table class="tg" align="center" width="1000">
  <tr>
    <th class="tg-baqh">说明</th>
  </tr>
  <tr>
    <td style="line-height:150%">
        &nbsp;1、“名称”用来标识采样结果，建议英文，不可为空;<br />
        &nbsp;2、“URL请求地址”填写需要采样的URL，可模糊匹配，不可为空;<br />
        &nbsp;3、“采样频率”取值范围为0.01-1，不可为空;
    </td>
  </tr>
</table>
</body>
</html>
