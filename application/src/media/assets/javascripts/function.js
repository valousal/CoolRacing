// ---------------------------------------------------------------------------------- //
// ---------------------------- Page de recherche ----------------------------------- //
// ---------------------------------------------------------------------------------- //

// ------- Afficher plus/moins options de recherche:


function displayOptions(){
	$('#options-search').toggle();
	if($('#btn-display-options').text() === 'Masquer les options'){
		$('#btn-display-options').html('Afficher plus d\'options');
	}else{
		$('#btn-display-options').html('Masquer les options');
	}
	return false;
}


// ---------------------------------------------------------------------------------- //
// ---------------------------- Boutons de scroll ----------------------------------- //
// ---------------------------------------------------------------------------------- //

// Fonction bouton de scroll pour diapo:
function scrollDiv(divId, depl) {
	var scroll_container = document.getElementById(divId);
	scroll_container.scrollLeft += depl;	
}


// ---------------------------------------------------------------------------------- //
// ----------------------------------- Panel ---------------------------------------- //
// ---------------------------------------------------------------------------------- //

// ------- Afficher/masquer panel :

function displayPanel(){
	// $('.panel').slideToggle();
	$('.panel').animate({'width': 'toggle'});
	if($(window).width() <= 800){
		$('#fog').toggle();
	}
}

function windowPanelResize(){
	$('#fog').hide();
	if($(window).width() <= 800){
		$('.panel').hide();
	} else {
		$('.panel').show();
	}
}


// ---------------------------------------------------------------------------------- //
// ----------------------------------- Form  ---------------------------------------- //
// ---------------------------------------------------------------------------------- //

// ------- verif All :
function verifAllParticipant(){
	if( verifText($('#name')) && verifText($('#firstname')) && verifDate($('#date')) && verifMail($('#mailpublic')) 
		&& verifMail($('#mailprivate')) && verifPhone($('#tel')) && verifIsset($('#num')) && verifIsset($('#postal'))
		&& verifIsset($('#rue')) && verifIsset($('#ville'))  && verifLogin($('#login')) &&  verifMdp() 
	){
		$('#submit').attr("disabled", false);
		return true;
	}else{
		// $('#submit').attr("disabled", true);
		return false;
	}
}

// ------- verif All :
function verifAllOrganisateur(){
	if(
		verifText($('#name-org')) && verifText($('#type-org')) && verifMail($('#mail-org')) && verifPhone($('#tel'))
		&& verifIsset($('#num')) && verifIsset($('#postal')) && verifIsset($('#rue')) && verifIsset($('#ville'))
		&& verifText($('#name')) && verifText($('#firstname')) && verifLogin($('#login')) &&  verifMdp() 
		&& verifMail($('#mail-private')) && verifMail($('#mail-contact'))
	){
		$('#submit').attr("disabled", false);
		return true;
	}else{
		// $('#submit').attr("disabled", true);
		return false;
	}
}

// ------- verif isset :
function verifIsset(elts){
	var value = elts.val();
	if ( value.length >= 1){
		$(elts).css("border-color", "#00BD01");
		return true;
	}else{
		// $(elts).css("border-color", "#D2000C");
		return false;
	}
}


// ------- verif text :
function verifText(elts){
	var value = elts.val();
	if ( value.length >= 2){
		$(elts).css("border-color", "#00BD01");
		return true;
	}else{
		// $(elts).css("border-color", "#D2000C");
		return false;
	}
}

// ------- verif login :
function verifLogin(elts){
	var value = elts.val();
	if ( value.length >= 4){
		$(elts).css("border-color", "#00BD01");
		return true;
	}else{
		// $(elts).css("border-color", "#D2000C");
		return false;
	}
}

// --------- verif mail:

function verifMail(elts){
	var value = elts.val();
	var reg = new RegExp('^[a-z0-9]+([_|\.|-]{1}[a-z0-9]+)*@[a-z0-9]+([_|\.|-]{1}[a-z0-9]+)*[\.]{1}[a-z]{2,6}$', 'i');
	if(reg.test(value)){
		$(elts).css("border-color", "#00BD01");
		return true;
	}
	else{
		// $(elts).css("border-color", "#D2000C");
		return false;
	}
}

// --------- verif mdp:

function verifMdp(){
	var value1 = $('#mdp').val();
	var value2 = $('#mdp-valid').val();
	if(value1.length >= 8 && value1==value2){
		$('#mdp').css("border-color", "#00BD01");
		$('#mdp-valid').css("border-color", "#00BD01");
		return true;
	}
	else{
		// $('#mdp').css("border-color", "#D2000C");
		// $('#mdp-valid').css("border-color", "#D2000C");
		return false;
	}
}

// ---------- verif date:

function verifDate(elts) {
   var value = elts.val();
	var reg = new RegExp(/^([0-2][0-9]|3[0-1])\/(0[0-9]|1[0-2])\/[0-9]{4}/gi);
 
    if(reg.test(value)){
        $(elts).css("border-color", "#00BD01");
        return true;
    }
    else{
        // $('#date').css("border-color", "#D2000C");
        return false;
    }
}

// --------- verif phone


function verifPhone(elts){
	var value = elts.val();
	var reg = new RegExp(/^(0[0-9])(-[0-9]{2}){4}/gi);
 
    if(reg.test(value)){
        $('#tel').css("border-color", "#00BD01");
        return true;
    }
    else{
        // $('#tel').css("border-color", "#D2000C");
        return false;
    }
}






