<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
 "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
   <title>
        <?php echo $session->read('Site.site_name'); ?>
		<?php echo ' :: ' . $title_for_layout; ?>
   </title>
   <?php
        echo $html->meta('icon');
		echo $html->css('rfg');        
		echo $html->css('style.css?20101003');        
        echo $html->script('jquery');
        echo $html->script('jquery.scrollto.min');
        echo $html->script('global');
        echo $scripts_for_layout;
    ?>
</head>
<body>
<div class="tophdw">
    <div id="tophd" class="tophdc">
        <?php
            echo $html->image('logo');
        ?>
        <h1>
            <?php echo $html->link($session->read('Site.site_name'), '/');?>
        </h1>
        <div id="tophdnav">
            <nobr>
                <span>Welcome back, </span><strong><?php echo $profile['name'];?></strong>
                |
                <span><?php echo $html->link(__('Home', true), '/');?></span> |
                <span><?php echo $html->link(__('Preferences', true), '/users/preferences');?></span> |
                <span><?php echo $html->link(__('Help', true), '#');?></span> |
                <span><?php echo $html->link(__('Logout', true), '/users/logout');?></span>
            </nobr>
        </div>
        <div class="clear"></div>
    </div>
</div>
<div class="tophdw" id="subtophdw">
    <div id="subtophd" class="tophdc">
        <!--
        <div class="heading headingFirst">Simple Search</div>
        <form method="get" action="index.php" id="simpleSearch" name="simpleSearch">
            <input type="text" name="keywords"/>
            <input type="submit" class="button marginTop" value="Pencarian" name="search"/>
        </form>
        <span class="headingSeparator">|</span>
        <div id="headingHelp" class="heading">
            <a href="index.php?p=help" class="menu">Search Help</a>
        </div>
        <span class="headingSeparator">|</span>
        <div id="headingLang" class="heading">Select Language</div>
        <form method="get" action="index.php" id="langSelect" name="langSelect">
            <select onchange="document.langSelect.submit();" style="width: 99%;" name="select_lang">
                <option value="de_DE">Jerman</option>
                <option value="en_US">Inggris</option><option selected="" value="id_ID">Indonesia</option>
            </select>
        </form>
        -->
    </div>
</div>
<div id="custom-doc" class="yui-t2">
    <div id="hd" role="banner">
        <?php
            if ($session->check('Message.flash')):
                echo $session->flash();
                echo $html->scriptBlock('$(function(){$.scrollTo( "#hd", 1000);});');
            endif;
        ?>
    </div>
    <div id="bd" role="main">
        <div id="yui-main">
            <div class="yui-b">
                <div class="yui-g">
                    <div id="maincontent" class="controllerName actionName">
                        <?php echo $content_for_layout;?>
                    </div>
                </div>
            </div>
        </div>
        <div role="navigation" class="yui-b" id="sb">
            <?php echo $this->element('menu.sidebar', array(
                    'cache' => array('time'=> "+14 days", 'key' => 'group_id_' . $profile['group_id'])
                ));
            ?>
        </div>	
	</div>
    <div id="ft" role="contentinfo">
        <div id="ftl">
            <p>
                <strong><?php echo $session->read('Site.site_name');?></strong>
                <br/>
                <?php echo $session->read('Site.site_address1');?><br />
                <?php echo $session->read('Site.site_address2');?>
                <br />
                <?php echo $session->read('Site.site_email');?> &mdash Telp. : <?php echo $session->read('Site.site_phone');?>
            </p>
        </div>
        <div id="ftr">
            <p>
                Copyright &copy; <?php echo date('Y');?>, <?php echo $session->read('Site.site_name');?>,<br />
                All Rights Reserved
            </p>
            <?php
                if ( isset($profile['last_login']) ) {
                    echo 'Anda login terkahir kali ' . $time->format('d/m/Y H:i', $profile['last_login']);
                }
            ?>
        </div>
        <div class="clear"></div>
    </div>
</div>
<div id="loading"></div>
<?php echo $this->element('sql_dump'); ?>
</body>
</html>
