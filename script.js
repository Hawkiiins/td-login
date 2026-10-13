"use strict";
const statut = document.getElementById("statut");
document.querySelectorAll("form[data-loader]").forEach((form) => {
  const bouton = form.querySelector("button[type='submit']");
  const texteInitial = bouton.textContent;
  form.addEventListener("submit", () => {
    bouton.disabled = true;
    bouton.setAttribute("aria-busy", "true");
    bouton.textContent = "Envoi en cours…";
    if (statut) statut.textContent = "Envoi en cours…";
  });
  window.addEventListener("pageshow", () => {
    bouton.disabled = false;
    bouton.removeAttribute("aria-busy");
    bouton.textContent = texteInitial;
    if (statut) statut.textContent = "";
  });
});