<?php 
/**
  * javaslatok listája
  * Licensz: GNU/GPL
  * Szerző: Fogler Tibor   tibor.fogler@gmail.com_addref
  * web: github.com/utopszkij/elovalasztok2018
  * Verzió: V1.00  2016.09.14.
  *
  */

?>
<div id="javaslatok">
	<h2>Jelölt javaslatok</h2>
	<p class="help">
		Budapesti lakosok javasolhat jelölteket az előválasztásra. A javaslatok a szerkesztő bizottság ellenörzése után
		kerülnek fel erre az oldalra. Az ellenörzés kizárólag stilisztikai, helyesírási, a szöveg valóság tartalmára
		terjed ki. Továbbá a szerkesztő bizottság kapcsolatba lép a jelölttel, és beleegyezését kéri az adatkezeléshez.
		A javasolt jelölt akkor fog  szerepelni a szavazó lapon ha legalább 500 támogatást szerez.
		A támogatást a budapest lakosok  ezen az oldalon a "támogatom" gomb segitségével jelezhetik. 
	</p>
	<div id="javaslatoklistaja">
		<?php foreach ($javaslatok as $javaslat) : ?>
 		<div class="item" style="clear:both">
 			<?php if ($javaslat->tamogatottsag == '') $javaslat->tamogatottsag = '0'; ?>
 			<h4><?php echo $javaslat->title; ?></h4>
 			<p class="introtext"><?php echo $javaslat->introtext; ?></p>
 			<p class="bovebben" style="clear:both">
				<a href="component/elovalasztok?task=javaslat&id=<?php echo $javaslat->id; ?>">Bővebben ...</a> 			
 			</p>
 			
 			
 			<?php if (($user->id > 0) && (strpos($user->params, 'budapest') > 0)) : ?>
 			<p class="tamogatas" style="clear:both">
				Támogatottság:<var><?php echo $javaslat->tamogatottsag; ?></var>&nbsp;
				<?php if ($javaslat->tamogatod <= 0) : ?>
					<a href="component/elovalasztok?task=tamogatom&id=<?php echo $javaslat->id; ?>" class="btn btn-success">
						<i class="fa fa-check"></i>Támogatom</a>&nbsp;
				<?php endif; ?>
				<?php if ($javaslat->tamogatod > 0) : ?>
					Támogattad&nbsp;
					<a href="component/elovalasztok?task=nemtamogatom&id=<?php echo $javaslat->id; ?>" class="btn btn-danger">
						Mégsem támogatom</a>&nbsp;
				<?php endif; ?>	
 			</p>
 			<?php endif; ?>
		</div> 	
		<?php endforeach; ?>
	</div>
	<p>&nbsp;</p>
	<?php if (($user->id > 0) && (strpos($user->params, 'budapest') > 0)) : ?>
	<div class="ujjelolt">
		<a href="component/elovalasztok?task=javaslatform" class="btn btn-primary">
			<i class="fa fa-plus-circle"></i>Új javaslat beküldése</a>
	</div>
	<p>&nbsp;</p>
	<?php endif; ?>
</div>