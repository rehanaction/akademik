// variabel global
var pagewali;
var formwali = "waliform";

function goSubmitWali() {
	document.getElementById(formwali).action = getPage(pagewali);
	document.getElementById(formwali).submit();
}

function goKRS() {
	pagewali = "set_krs";
	goSubmitWali();
}

function goKHS() {
	pagewali = "view_khs";
	goSubmitWali();
}

function goTranskrip() {
	pagewali = "view_transkrip";
	goSubmitWali();
}

function goKemajuan() {
	pagewali = "view_kemajuanbelajar";
	goSubmitWali();
}

function goSwitch() {
	document.getElementById("npm").value = document.getElementById("npmtemp").value;
	goSubmit();
}

function goFirstNIM() {
	document.getElementById("act").value = "first";
	goSubmit();
}

function goPrevNIM() {
	document.getElementById("act").value = "prev";
	goSubmit();
}

function goNextNIM() {
	document.getElementById("act").value = "next";
	goSubmit();
}

function goLastNIM() {
	document.getElementById("act").value = "last";
	goSubmit();
}