<?php
/**
  * új javaslat ürlap
  * Licensz: GNU/GPL
  * Szerző: Fogler Tibor   tibor.fogler@gmail.com_addref
  * web: github.com/utopszkij/elovalasztok2018
  * Verzió: V1.00  2016.09.14.
  *
  */
?>
<div id="javaslatForm">
	<h2>Javaslat a 2019-es főpolgármester előválasztás jelöltjére</h2>
	<form method="post" action="component/elovalasztok?task=javaslatsave">
		<p class="help">
			Az ezen a képernyőn megadott adatok a szerkesztő bizottság
			technikai ellenörzése, és a jelölt személy hozzájárulása után fognak megjelenni a nyilvános web oldalon.		
		</p>	
		<p><label>Jelölt neve *</label>
		<input type="text" class="nev" name="nev" size="80" /></p>
		<p><label>Fénykép URL (png vagy jpg) *</label>
		<input type="text" class="url" name="kepUrl" size="80" /></p>
		<p><label>Életrajz<br /><br />
		</label>
		<textarea rows="10" cols="80" class="eletrajz" name="eletrajz" /></textarea>
		<p><label>Budapesti politikai program *</label>
		<textarea rows="10" cols="80" class="program" name="program" /></textarea>
		<p><label>Támogató szervezetek</label>
		<input type="text" class="tamogatok" name="tamogatok" /></p>
		<p><label>Elérhetőség (telfon, email stb) *<br />Ezek az adatok nem kerülnek nyilvános publikálásra,
		 a rendszer adminisztrátor ezek segitségével kéri ki a jelölt hozzájárulását az adatkezeléshez.</label>
		<input type="text" class="kontakt" name="kontakt" /></p>
		<p></p>
		<p class="submitBtn"><button type="submit" class="btn btn-primary">Beküldés</button></p>
		<p>A * -al jelölt mezők kitöltése kötelező.</p>
		<p></p>
	</form>
</div>