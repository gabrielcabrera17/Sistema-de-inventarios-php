<p class="has-text-right pt-4 pb-4">
		<a href="#" class="button is-link is-rounded btn-back"><- Regresar atrás</a>
	</p>
	<script type="text/javascript">
        //Se selecciona ese boton mediante su clase que es .btn-black
	    let btn_back = document.querySelector(".btn-back");
        //Una vez se selcciona se asigna un evento a ese elemento btn
        //Cuando le doy click 
	    btn_back.addEventListener('click', function(e){
	        e.preventDefault();
	        window.history.back();
	    });
</script>