<?php
defined('ROOT_PATH') ? "" : die("can't run alone");
$checkObj = new checkConfig();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>JellyCMS安装向导(二)</title>
<link rel="icon" href="./../favicon.ico" type="image/x-icon" />
<link rel="shortcut icon" href="./../favicon.ico" type="image/x-icon" />
<link rel="stylesheet" href="css/install.css" />
</head>
<body>
<div class="container">
	<div class="head"><img src="images/logo.png" width="354" height="53" alt="JellyCMS安装向导" />安装向导</div>
	<div class="ins_box clearfix">
		<div class="cont clearfix">
			<ul class="step">
				<li id="step_1"><span>1</span>许可协议</li>
				<li id="step_2" class="current"><span>2</span>系统检测</li>
				<li id="step_3"><span>3</span>系统配置</li>
				<li id="step_4"><span>4</span>安装完成</li>
			</ul>
			<div class="log_box">
				<h2>系统及目录检测</h2>

				<div class="green_box" style='display:none' id='right_div'>
					<img src="images/success.png" width="19" height="18" />
					您的系统配置是有效的，单击下一步继续！
				</div>

				<div class="red_box" style='display:none' id='error_div'>
					<img src="images/error.png" width="16" height="15" />
					您的系统配置不具备安装JellyCMS软件，有疑问可以访问：<a href='https://bbs.guodong.tech/' target='_blank'>https://bbs.guodong.tech/</a>
				</div>

				<div class="gray_box">
					<div class="box">
						<strong>PHP版本及环境设置</strong>
						<?php //phpversion检查
						$phpVersion_pass = $checkObj->c_phpVersion();?>
						<p><img src="images/<?php echo $phpVersion_pass ? 'success' : 'failed';?>.png" width="16" height="16" />PHP <?php echo $checkObj->getPHPVersion();?></p>

						<?php //phpini检查
						$phpiniArray = $checkObj->c_phpIni();
						foreach($phpiniArray as $key => $val)
						{
						?>
						<p><img src="images/<?php echo $val ? 'success' : 'failed';?>.png" width="16" height="16" /><?php echo $key;?><?php if(!$val){?><label><?php echo configInfo($key);?></label><?php }?></p>
						<?php
						}
						?>

						<strong>必须扩展配置</strong>
						<?php //must_extension检查
						$mustExtensionArray = $checkObj->c_must_extension();
						foreach($mustExtensionArray as $key => $val)
						{
						?>
						<p><img src="images/<?php echo $val ? 'success' : 'failed';?>.png" width="16" height="16" /><?php echo $key;?><?php if(!$val){?><label><?php echo configInfo($key);?></label><?php }?></p>
						<?php
						}
						?>

						<strong>必须启用函数</strong>
						<?php //php_function检查
						$mustFunctionArray = $checkObj->c_functionExists();
						foreach($mustFunctionArray as $key => $val)
						{
						?>
						<p><img src="images/<?php echo $val ? 'success' : 'failed';?>.png" width="16" height="16" /><?php echo $key;?><?php if(!$val){?><label><?php echo configInfo($key);?></label><?php }?></p>
						<?php
						}
						?>

						<strong>建议扩展配置</strong>
						<?php //recom_extension检查
						$recomExtensionArray = $checkObj->c_recom_extension();
						foreach($recomExtensionArray as $key => $val)
						{
						?>
						<p><img src="images/<?php echo $val ? 'success' : 'failed';?>.png" width="16" height="16" /><?php echo $key;?><?php if(!$val){?><label><?php echo configInfo($key);?></label><?php }?></p>
						<?php
						}
						?>

						<strong>文件可写权限</strong>
						<?php //writeable
						$writeableArray = $checkObj->c_writeableDir();
						foreach($writeableArray as $key => $val)
						{
						?>
						<p><img src="images/<?php echo $val ? 'success' : 'failed';?>.png" width="16" height="16" /><?php echo $key;?><?php if(!$val){?><label><?php echo configInfo($key);?></label><?php }?></p>
						<?php
						}
						?>

						<strong>文件可读权限</strong>
						<?php //writeable
						$readableArray = $checkObj->c_readableDir();
						foreach($readableArray as $key => $val)
						{
						?>
						<p><img src="images/<?php echo $val ? 'success' : 'failed';?>.png" width="16" height="16" /><?php echo $key;?><?php if(!$val){?><label><?php echo configInfo($key);?></label><?php }?></p>
						<?php
						}
						?>

					</div>
				</div>

			</div>
			<p class="operate">
				<button class="return" type="button" onclick="window.location.href = 'index.php?act=install';">返回</button>
				<button class="next" type="button" onclick="check_config();">下一步</button>
			</p>
		</div>
		
	</div>
	<div class="foot"><a href="https://www.jellycms.cn">关于我们</a>|<a href="https://www.jellycms.cn">官方网站</a>|<a href="https://www.jellycms.cn">联系我们</a>|<a href="">©2015-2020</a></div>
</div>

<script type='text/javascript'>
	ErrorNum = <?php echo $checkObj->getNpassMustNum();?>

	//检查配置信息
	function check_config()
	{
		var error_num = ErrorNum;
		if(error_num > 0)
		{
			alert('您的系统环境配置没有通过检查');
		}
		else
		{
			window.location.href = 'index.php?act=install_3';
		}
	}

	if(ErrorNum > 0)
	{
		document.getElementById('error_div').style.display = '';
	}
	else
	{
		document.getElementById('right_div').style.display = '';
	}
</script>
</body>
</html>
