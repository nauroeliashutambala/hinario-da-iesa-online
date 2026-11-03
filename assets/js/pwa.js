(() => {
  "use strict";

  const installButton = document.querySelector("#pwaInstallButton");
  const status = document.querySelector("#pwaStatus");
  let deferredPrompt = null;

  const setStatus = message => {
    if (status) status.textContent = message;
  };

  if (!("serviceWorker" in navigator)) {
    setStatus("Este navegador não suporta o modo offline instalado.");
    return;
  }

  navigator.serviceWorker.register("/service-worker.js")
    .then(async () => {
      await navigator.serviceWorker.ready;
      setStatus(navigator.onLine
        ? "Biblioteca preparada para uso offline neste dispositivo."
        : "Sem ligação. A biblioteca guardada continua disponível.");
    })
    .catch(error => {
      console.error("[Hinário] Não foi possível ativar o modo offline.", error);
      setStatus("Não foi possível preparar o modo offline. Verifica a ligação e tenta novamente.");
    });

  window.addEventListener("beforeinstallprompt", event => {
    event.preventDefault();
    deferredPrompt = event;
    if (installButton) installButton.hidden = false;
  });

  installButton?.addEventListener("click", async () => {
    if (!deferredPrompt) {
      setStatus("Usa o menu do navegador e escolhe “Instalar aplicação”.");
      return;
    }

    try {
      await deferredPrompt.prompt();
      const choice = await deferredPrompt.userChoice;
      if (choice.outcome === "accepted") {
        setStatus("A aplicação foi adicionada ao dispositivo.");
      }
      deferredPrompt = null;
      installButton.hidden = true;
    } catch (error) {
      console.error("[Hinário] Não foi possível iniciar a instalação.", error);
      setStatus("A instalação não foi concluída. Tenta novamente pelo menu do navegador.");
    }
  });

  window.addEventListener("appinstalled", () => {
    deferredPrompt = null;
    if (installButton) installButton.hidden = true;
    setStatus("Aplicação instalada. A biblioteca está disponível offline.");
  });

  window.addEventListener("offline", () => {
    setStatus("Sem ligação. A biblioteca guardada continua disponível.");
  });

  window.addEventListener("online", () => {
    setStatus("Ligação restabelecida. A biblioteca está sincronizada.");
  });
})();
