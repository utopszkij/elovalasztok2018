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
	<form method="post" name="form0" action="component/elovalasztok?task=javaslatsave">
		<p class="help">
			Az ezen a képernyőn megadott adatok a szerkesztő bizottság
			technikai ellenörzése, és a jelölt személy hozzájárulása után fognak megjelenni a nyilvános web oldalon.
			A jelölt képét egy nyilvános kép megosztó olfalra (pl. instagram, facebook) kell feltölteni mindenki
			számára elérhetően, és itt ennek címét adjuk meg,		
		</p>	
		<?php
			$session = JFactory::getSession();
			$csrToken = $session->getFormToken();
			$session->set('myCsrToken',$csrToken); 
			echo '<input type="hidden" name="'.$csrToken.'" value="1" />';
		?>
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
		<input type="text" class="tamogatok" name="tamogatok" size="80" /></p>
		<p><label>Elérhetőség (telfon, email stb) *<br />Ezek az adatok nem kerülnek nyilvános publikálásra,
		 a rendszer adminisztrátor ezek segitségével kéri ki a jelölt hozzájárulását az adatkezeléshez.</label>
		<input type="text" class="kontakt" name="kontakt" size="80" /></p>
		<p></p>
		<p class="submitBtn"><button type="button" onclick="okClick()" class="btn btn-primary">
			Beküldés
			</button></p>
		<p>A * -al jelölt mezők kitöltése kötelező.</p>
		<p></p>
	</form>
</div>


<script type="text/javascript">
	function okClick() {
		var s = '';
		if (document.forms.form0.nev.value == '') {
			s += 'Nevet meg kell adni!<br />';
		}
		if (document.forms.form0.kepUrl.value == '') {
			s += 'Kép URL -t meg kell adni!<br />';
		}
		if (document.forms.form0.program.value == '') {
			s += 'Programot meg kell adni!<br />';
		}
		if (document.forms.form0.kontakt.value == '') {
			s += 'Kapcsolat felvételi adatokat meg kell adni!<br />';
		}
		if (s == '') {
			document.forms.form0.submit();
		} else {
			popupAlert(s);
		}	
	}
</script>