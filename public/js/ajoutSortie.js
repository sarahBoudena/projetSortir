//Déclaration des constantes
const VILLE = "Ville  : ";
const RUE = "Rue  : ";
const CP = "Code Postal  : ";
const LATITUDE = "Latitude : ";
const LONGITUDE = "Longitude : ";


//Déclaration des variables
let selectLieu = document.getElementById("selectLieu");
let affichage = document.getElementById("detailsLieu");
let lieuSelectionne = document.getElementById("selectLieu").value;


//Observer
selectLieu.addEventListener("change", afficherDetails);


//Fonction afficherDetails
function afficherDetails() {

}