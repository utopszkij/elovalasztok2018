<?php
/**
 * @package     Joomla.Site
 * @subpackage  Templates.beez3
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;
JLoader::import('joomla.filesystem.file');
JHtml::_('behavior.framework', true);
// JHtml::_('bootstrap.framework');
JHtml::_('script', 'jui/html5.js', array('version' => 'auto', 'relative' => true, 'conditional' => 'lt IE 9'));

$this->setHtml5(true);
$session = JFactory::getSession();
$input = JFactory::getApplication()->input;

$app       = JFactory::getApplication(); // Access the Application Object
$menu      = $app->getMenu(); // Load the JMenuSite Object
$active    = $menu->getActive(); // Load the Active Menu Item as an stdClass Object

// cookie manager step 1.
if ($input->get('cookieenable','3') == 0) {
	$session->set('cookie_enable',0);
	$_COOKIE['cookie_enable'] = 0;
	setcookie("cookie_enable",0,time()+(60*60*24*120));
} else 	if ($input->get('cookieenable','3') == 1) {
	$session->set('cookie_enable',1);
	setcookie("cookie_enable",1,time()+(60*60*24*120));
} else if (isset($_COOKIE['cookie_enable'])) {
	$session->set('cookie_enable',$_COOKIE['cookie_enable']);
}

?>

<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
	<head>
		<meta charset="utf-8" />
		<link rel="apple-touch-icon" sizes="76x76" href="assets/img/apple-icon.png">
		<link rel="icon" type="image/png" href="assets/img/favicon.png">	
		<title>Elovalasztas</title>
		<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
		<meta name="viewport" content="width=device-width" />

		<meta name="generator" content="Bluefish 2.2.10" />
		<base href="<?php echo JURI::base(); ?>" />
		<link href="/netpolgar/templates/utopszkij/favicon.ico" rel="shortcut icon" type="image/vnd.microsoft.icon" />

		<!-- jquery -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

		<!-- bootstrap -->
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
		<!-- link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" -->
		<!-- script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script -->		
		<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>
		<script>
			var RESTRICTED = {};
		</script>
	
		<!-- awesome font -->	
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		<!-- font -->
		<link href='http://fonts.googleapis.com/css?family=Grand+Hotel' rel='stylesheet' type='text/css'>
		<!-- main css -->
		<link rel="stylesheet" href="<?php echo JURI::base(); ?>templates/utopszkij/css/template.css">

	</head>
	<body>
        <noscript>
            <div class="noscript>">
                <h3>A böngészőjében nincs engedélyezve a javascript használata.</h3>
                <p>Ennek a rendszernek a használatához szükség van a javascript engedélyezésére.</p>
                <p>Engedélyezze a böngésző beállításoknál, majd frissitse ezt az oldalt!</p>
            </div>
        </noscript>	

		<div id="popup" style="position:absolute; left:80px; top:80px; width:400px; height:650px; z-index:1000; padding:10px; border-style:solid; border-width:2px; background-color:#E0E0F0; display:none">
			<iframe width="100%" height="630" id="popupIfrm"></iframe>
		</div>	
	
		<div class="menuContainer" id="topMenuContainer">
			<div id="topMenuIcon">
				<i class="fa fa-bars" onclick="$('#topMenu').toggle()" style="cursor:pointer"></i>
			</div>
			<div id="logo">
				<a href="index.php">
					<var style="color:white; font-size:10pt; margin:3px;"><strong>Főpolgármester előválasztás 2018</strong></var>
				</a>
			</div>
			<?php 
				// userinfo
				$user = JFactory::getUser();
				if ($user) {
					if ($user->id > 0)
						echo '<div id="userInfo"><i class="fa fa-key"></i></div>';
				}			
			?>
			<div id="topMenu">
				<jdoc:include type="modules" name="position-7" />
			</div>
			<div style="clear:booth"></div>
		</div><!-- topMenuContainer -->

		<div id="navbar-full">
			
			<div class="homePage">
			<?php if (JFactory::getApplication()->input->get('Itemid') == 106) : ?>
			<div id="carouselIndicators" class="carousel slide" data-ride="carousel">
			  <ol class="carousel-indicators">
				<li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
				<li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
				<li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
			  </ol>
			  <div class="carousel-inner">
				<div class="carousel-item active">
				  <img class="d-block w-100" src="./templates/utopszkij/assets/img/entrepreneur-3245868_1920.jpg" alt="">
				  <div class="carousel-caption d-none d-md-block">
					<h5>Élhető, modern világvárost!</h5>
					<p>
						<a class="btn" href="<?php echo JURI::base()?>leiras">Ismertető</a>
						<a class="btn" href="<?php echo JURI::base()?>login">Bejelentkezés</a>
					</p>
				  </div>
				</div>
				<div class="carousel-item">
				  <img class="d-block w-100" src="./templates/utopszkij/assets/img/city-1215052.jpg" alt="Csináljunk egy jobb világot!">
				  <div class="carousel-caption d-none d-md-block">
					<h5>Fejlett közszolgáltatásokat!</h5>
					<p>
						<a class="btn" href="<?php echo JURI::base()?>leiras">Ismertető</a>
						<a class="btn" href="<?php echo JURI::base()?>login">Bejelentkezés</a>
					</p>
				  </div>
				</div>
				<div class="carousel-item">
				  <img class="d-block w-100" src="./templates/utopszkij/assets/img/windmill-3322529_1920.jpg" alt="Nem az a szabadság, hogy négy évente megválasztjuk ki uralkodjon felettünk.">
				  <div class="carousel-caption d-none d-md-block">
					<h5>Jó levegőt, kultúrált környzetet!</h5>
					<p>
						<a class="btn" href="<?php echo JURI::base()?>leiras">Ismertető</a>
						<a class="btn" href="<?php echo JURI::base()?>login">Bejelentkezés</a>
					</p>
				  </div>
				</div>
			  </div>
              <!--  
			  <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
				<span class="carousel-control-prev-icon" aria-hidden="true"></span>
				<span class="sr-only">&lt;&lt;&lt;</span>
			  </a>
			  <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
				<span class="carousel-control-next-icon" aria-hidden="true"></span>
				<span class="sr-only">&gt;&gt;&gt;</span>
			  </a>
              -->  
			</div>			
			
			
			<?php endif; ?>
			</div><!-- homepage -->
			

			<!-- main page -->
			<div id="main" style="z-index:9; background-color:white">
				<center>
				<div class="tim-container">
						<div class="row">
							<div class="col-md-12 col-sd-12">
								<div id="breadcrumbs">
									<jdoc:include type="modules" name="position-2" />
								</div>
							</div>
						</div>	
						<div class="row">
							<div class="col-md-12 col-sd-12">
								<div id="messages">
									<jdoc:include type="message" />
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12 col-sd-12">
								<div id="position-1">
									<jdoc:include type="modules" name="position-1" />
								</div>
								<div id="position-3">
									<jdoc:include type="modules" name="position-3" />
								</div>
							</div>
						</div>
						<?php if (JFactory::getApplication()->input->get('Itemid') != 106) : ?>
						<div class="row" id="component">
							<div class="col-md-12 col-sd-12">
									<jdoc:include type="component" />
							</div>
						</div>
						<?php endif; ?>
						<div class="row">
							<div class="col-md-12 col-sd-12">
								<div id="position-4">
									<jdoc:include type="modules" name="position-4" />
								</div>
								<div id="position-5">
									<jdoc:include type="modules" name="position-5" />
								</div>
								<div id="position-6">
									<jdoc:include type="modules" name="position-6" />
								</div>
								<div id="position-8">
									<jdoc:include type="modules" name="position-8" />
								</div>
								<div id="position-9">
									<jdoc:include type="modules" name="position-9" />
								</div>
								<div id="position-10">
									<jdoc:include type="modules" name="position-10" />
								</div>
								<div id="position-11">
									<jdoc:include type="modules" name="position-11" />
								</div>
								<div id="position-12">
									<jdoc:include type="modules" name="position-12" />
								</div>
								<div id="position-14">
									<jdoc:include type="modules" name="position-13" />
								</div>
							</div>	
						</div> <!-- row -->
						
						<section id="temak">
							<center>
    							<div class="container">
                                    <div class="row">
										<div class="col-md-3 col-sm-6">
											<div class="tema-wrap">
											  <a href="login">
												<i class="fa fa-sign-in <?php if ($user->id > 0) echo ' disabled'; ?>"></i>
												<h2>Bejelentkezek</h2>
											  </a>	
											</div>
										</div><!--/.col-md-3-->
										<div class="col-md-3 col-sm-6">
											<div class="tema-wrap">
											  <a href="https://adatom.hu/fiokom.html?section=register">
												<i class="fa fa-key  <?php if ($user->id > 0) echo ' disabled'; ?>"></i>
												<h2>Regisztrálok</h2>
											  </a>	
											</div>
										</div><!--/.col-md-3-->
										<div class="col-md-3 col-sm-6">
											<div class="tema-wrap">
											  <a href="logout">
												<i class="fa fa-sign-out <?php if ($user->id <= 0) echo ' disabled'; ?>"></i>
												<h2>Kijelentkezek</h2>
											  </a>	
											</div>
										</div><!--/.col-md-3-->
										<div class="col-md-3 col-sm-6">
											<div class="tema-wrap">
											  <a href="component/elovalasztok">
												<i class="fa fa-envelope  <?php if ($user->id <= 0) echo ' disabled'; ?>"></i>
												<h2>Szavazok</h2>
											  </a>	
											</div>
										</div><!--/.col-md-3-->
                                </div>

								<div class="row">
										<div class="col-md-3 col-sm-6">
											<div class="tema-wrap">
											  <a href="jelöltek">
												<i class="fa fa-users"></i>
												<h2>Jelöltek</h2>
											  </a>	
											</div>
										</div><!--/.col-md-3-->

										<div class="col-md-3 col-sm-6">
											<div class="tema-wrap">
											  <a href="component/elovalasztok?task=eredmeny">
												<i class="fa fa-signal"></i>
												<h2>Eredmény</h2>
											  </a>	
											</div>
										</div><!--/.col-md-3-->

										<div class="col-md-3 col-sm-6">
											<div class="tema-wrap">
											  <a href="leiras">
												<i class="fa fa-info"></i>
												<h2>Leírás</h2>
												<h3>Az előválasztás célja, müködése</h3>
											  </a>	
											</div>
										</div><!--/.col-md-3-->

										<div class="col-md-3 col-sm-6">
											<div class="tema-wrap">
											  <a href="adatkezeles">
												<i class="fa fa-lock"></i>
												<h2>Adatkezelési leírás</h2>
											  </a>	
											</div>
										</div><!--/.col-md-3-->
								</div><!--/.row-->    
                                <div class="row">
										<div class="col-md-3 col-sm-6">
											<div class="tema-wrap">
											  <a href="http://www.gnu.org/licenses/quick-guide-gplv3.html">
												<i class="fa fa-copyright"></i>
												<h2>Licensz</h2>
												<h3>GNU/GPL</h3>
												<a href="http://gnu.hu/gplv3.html">Magyar fordítás</a>
											  </a>	
											</div>
										</div><!--/.col-md-3-->
										<div class="col-md-3 col-sm-6">
											<div class="tema-wrap">
											  <a href="impresszum">
												<i class="fa fa-id-card"></i>
												<h2>Impresszum</h2>
											  </a>	
											</div>
										</div><!--/.col-md-3-->
										<div class="col-md-3 col-sm-6">
											<div class="tema-wrap">
											  <a href="https://github.com/utopszkij/elovalasztok2018">
												<i class="fa fa-code"></i>
												<h2>Forrás programm</h2>
											  </a>	
											</div>
										</div><!--/.col-md-3-->
										<div class="col-md-3 col-sm-6">
											<div class="tema-wrap">
                                                <a href="http://adatom.hu">
                                                    <img src="https://adatom.hu/images/logo/adalogo_379x143.png" style="width:80px;" />
                                                    <h2>Anonim Digitális Azonosító</h2>
                                                </a>
                                            </div>
                                        </div>

								</div><!--/.row-->    
							</div><!--/.container-->
							</center>
						</section><!--/#temak-->

						<center>
						<div id="recent-works">
								<div class="center wow fadeInDown">
									<h2>Ajánlott oldalak</h2>
								</div>

								<div class="row">
										<div class="recent-work-wrap">
											<div class="overlay">
												<div class="recent-work-inner">
													<h3><a href="#">Szellemi termelési mód</a> </h3>
													<p>Kapitány Ágnes és Gábor könyve egy lehetséges új termelési mód körvonalait vázolja.</p>
													<a href="https://hu.wikipedia.org/wiki/Szellemi_termel%C3%A9si_m%C3%B3d"><i class="fa fa-eye"></i>Megnézem</a>
												</div> 
											</div>
											<img src="templates/utopszkij/images/portfolio/recent/item1.png" alt="">
										</div>
										<div class="recent-work-wrap">
											<div class="overlay">
												<div class="recent-work-inner">
													<h3><a href="#">Internet filozófia</a></h3>
													<p></p>
													<a href="http://internetfilozofia.blog.hu/"><i class="fa fa-eye"></i>Megnézem</a>
												</div> 
											</div>
											<img src="templates/utopszkij/images/portfolio/recent/item2.png" alt="">
										</div>
										<div class="recent-work-wrap">
											<div class="overlay">
												<div class="recent-work-inner">
													<h3><a href="#">Információs társadalom</a></h3>
													<p>Wikipedia szó cikk.</p>
													<a href="https://hu.wikipedia.org/wiki/Inform%C3%A1ci%C3%B3s_t%C3%A1rsadalom_(fogalom)"><i class="fa fa-eye"></i>Megnézem</a>
												</div> 
											</div>
											<img src="templates/utopszkij/images/portfolio/recent/item3.png" alt="">
										</div>
										<div class="recent-work-wrap">
											<div class="overlay">
												<div class="recent-work-inner">
													<h3><a href="#">Katedrális és bazár</a></h3>
													<p>A "szabadszoftver" világ alapműve.</p>
													<a href="http://magyar-irodalom.elte.hu/robert/szovegek/bazar/"><i class="fa fa-eye"></i>Megnézem</a>
												</div> 
											</div>
											<img src="templates/utopszkij/images/portfolio/recent/item4.png" alt="">
										</div>
										<div class="recent-work-wrap">
											<div class="overlay">
												<div class="recent-work-inner">
													<h3><a href="#">Szelid pénz</a></h3>
													<p>Egy alternatív pénzrendszer....</p>
													<a href="http://edok.lib.uni-corvinus.hu/284/1/Szalay93.pdf"><i class="fa fa-eye"></i>Megnézem</a>
												</div> 
											</div>
											<img src="templates/utopszkij/images/portfolio/recent/item5.png" alt="">
										</div>
										<div class="recent-work-wrap">
											<div class="overlay">
												<div class="recent-work-inner">
													<h3><a href="#">Likvid demokrácia</a></h3>
													<p>A napjainkra már teljesen kiüresedett, funkcióját vesztett képviseleti demokrácia egy lehetséges utóda.</p>
													<a href="http://hu.alternativgazdasag.wikia.com/wiki/Likvid_demokr%C3%A1cia"><i class="fa fa-eye"></i>Megnézem</a>
												</div> 
											</div>
											<img src="templates/utopszkij/images/portfolio/recent/item6.png" alt="">
										</div>
										<div class="recent-work-wrap">
											<div class="overlay">
												<div class="recent-work-inner">
													<h3><a href="#">Vénusz projekt</a></h3>
													<p>Egy átfogó jövő kép...</p>
													<a href="https://www.youtube.com/watch?v=Uh9VxaO12zY&list=PL255C39DA73A5F10B&index=149"><i class="fa fa-eye"></i>Röviditett video</a><br>
													<a href="https://www.youtube.com/watch?v=JcbMW5Y5HxY"><i class="fa fa-eye"></i>Teljes video</a>
												</div> 
											</div>
											<img src="templates/utopszkij/images/portfolio/recent/item7.png" alt="">
										</div>
										<div class="recent-work-wrap">
											<div class="overlay">
												<div class="recent-work-inner">
													<h3><a href="#">Feltétel nélküli alapjövedelem</a></h3>
													<p>Ezt akár holnap megcsinálhatnánk....</p>
													<a href="http://alapjovedelem.hu/index.php/gyik"><i class="fa fa-eye"></i>Megnézem</a>
												</div> 
											</div>
											<img src="templates/utopszkij/images/portfolio/recent/item8.png" alt="">
										</div>
										<div class="recent-work-wrap">
											<div class="overlay">
												<div class="recent-work-inner">
													<h3><a href="#">Alternatív gazdaság</a></h3>
													<p>Nem a piacgazdaság az egyetlen elképzelhető mód a társadalmi munkamegosztás megszervezésére.</p>
													<a href="http://hu.alternativgazdasag.wikia.com/wiki/Alternat%C3%ADv_Gazdas%C3%A1g_lexikon"><i class="fa fa-eye"></i>Megnézem</a>
												</div> 
											</div>
											<img src="templates/utopszkij/images/portfolio/recent/finger-3139200_640.jpg" alt="">
										</div>
										<div class="recent-work-wrap">
											<div class="overlay">
												<div class="recent-work-inner">
													<h3><a href="#">Megosztás alapú gazdaság</a></h3>
													<h4>Ez is egy komcepció...</h4>
													<a href="https://medium.com/envienta-magyarorsz%C3%A1g/envienta-%C3%BAtban-egy-%C3%BAj-t%C3%A1rsadalom-fel%C3%A9-43e6b72c3a2c"><i class="fa fa-eye"></i>Megnézem</a>
												</div> 
											</div>
											<img src="templates/utopszkij/images/portfolio/recent/human-1157116_640.jpg" alt="">
										</div>
										
										
										
								</div><!--/.row-->
						</div><!--/#recent-works-->
						</center>
						
				</div><!-- container -->	
				</center>
			</div> <!-- main -->
		</div><!-- navbar-full -->

		<div class="row" style="padding:0px; margin:0px">
			<div class="col-md-12 col-sd-12">
				<footer id="footer">
					<jdoc:include type="modules" name="position-14" />
					<?php $shareLink = urlencode(JURI::current()); ?>
					<p class="shareIcons">			
						<i class="fa fa-share-alt"></i>&nbsp;
						<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $shareLink; ?>">
							<i class="fa fa-facebook" title="facebook"
							style="display:inline-block; background-color:blue; color:white; width:16px; text-align:center"></i>
						</a>&nbsp;									
						<a href="https://twitter.com/home?status=<?php echo $shareLink; ?>">
							<i class="fa fa-twitter"  title="twitter"
							style="background-color:cyan;"></i>
						</a>&nbsp;
						<a href="https://plus.google.com/share?url=<?php echo $shareLink; ?>">
							<i class="fa fa-google-plus"  title="google+"
							style="background-color:orange;"></i>
						</a>									
					</p>									
				</footer>
			</div>	
		</div>

		<div style="background-color:white; color:black; z-index:10; opacity:0.99">
            <a href="./impresszum">Impresszum</a>&nbsp;&nbsp;
            <a href="https://github.com/utopszkij/elovalasztok2018">Forrás program</a>&nbsp;&nbsp;
        </div>
        <div class="swInfo" style="background-color:#d0d0d0">
			<a target="new" href="https://fontawesome.com/icons?d=gallery">Awesome</a>&nbsp;
			<a target="new"  href="https://getbootstrap.com/docs/4.1/getting-started/introduction/">Bootstrap</a>&nbsp;
			<a target="new"  href="http://api.jquery.com/">JQuery</a>&nbsp;
			<a target="new"  href="https://api.joomla.org/cms-3/namespaces/Joomla.html">Joomla</a>&nbsp;
			<a target="new"  href="http://php.net/manual/en/index.php">PHP</a>&nbsp;
		</div>
		
	
	<?php
	// cookie manager step 2
	echo '<br /><br /><br /><br /><br />
	';
	if ($input->get('cookieenable','3') == 0) {
		$session->set('cookie_enable',0);
	}
	if ($input->get('cookieenable','3') == 1) {
		$session->set('cookie_enable',1);
	}
	if ($session->get('cookie_enable',3) == 1) {
		echo '
			<div id="gdpr">
				Ön hozzájárult, hogy programunk az Ön gépén cookie -kat kezeljen.
				<a class="btn btn-primary" 
					href="'.JURI::base().'index.php?cookieenable=0">
					Visszavonom a hozzájárulást
				</a>&nbsp;
			</div>
			';
			
			// itt lehet a statisztika követő kód
			
	} else {
			echo '
			<div id="gdpr">
				A rendszer használata közben bizonyos esetekben Önnel kapcsolatos adatokat kezelünk és adatokat (cookie -kat) tárolunk az Ön gépén. 
				&nbsp;<a target="new" href="adatkezeles">
					Erről itt olvashat részletesebben.
				</a>&nbsp;
				A vonatkozó rendelkezések értemében
					&nbsp;<a target="new" href="https://eur-lex.europa.eu/legal-content/HU/TXT/HTML/?uri=CELEX:32016R0679&from=HU">
							(lásd itt)
					</a>&nbsp;
				mindehez az Ön hozzájárulása szükséges.
				&nbsp;<a class="btn btn-primary" 
					href="'.JURI::base().'index.php?cookieenable=1">
					Hozzájárulok
				</a>&nbsp;
			</div>
			';
	}	
	?>
	
	<?php if (JFactory::getApplication()->input->get('Itemid') == 104) : ?>
	<script type="text/javascript">
		document.getElementById('popupIfrm').src = "<?php echo JURI::root(); ?>index.php?option=com_adalogin&view=adalogin&redi=index.php";
		jQuery('#popup').toggle();
	</script>	
	<?php endif; ?>
	
	</body>
</html>
