(() => {
  const root = document.documentElement;
  const form = document.querySelector("#fest-form");
  const themeButtons = document.querySelectorAll("[data-theme-set]");
  const tipoSelect = document.querySelector("#tipo");
  const preview = document.querySelector("#live-preview");

  const savedTheme = localStorage.getItem("megafest-theme") || "evento";
  setTheme(savedTheme);

  themeButtons.forEach((btn) => {
    btn.classList.toggle("active", btn.dataset.themeSet === savedTheme);
    btn.addEventListener("click", () => {
      setTheme(btn.dataset.themeSet);
      themeButtons.forEach((b) => b.classList.toggle("active", b === btn));
      if (tipoSelect && !tipoSelect.dataset.locked) {
        tipoSelect.value = btn.dataset.themeSet;
        toggleFields(btn.dataset.themeSet);
        updatePreview();
      }
    });
  });

  document.querySelectorAll("[data-filter]").forEach((btn) => {
    btn.addEventListener("click", () => {
      const tipo = btn.dataset.filter;
      document.querySelectorAll("[data-filter]").forEach((b) => b.classList.toggle("active", b === btn));
      document.querySelectorAll("[data-card-tipo]").forEach((card) => {
        card.style.display = tipo === "todos" || card.dataset.cardTipo === tipo ? "" : "none";
      });
    });
  });

  const dashFilter = document.querySelector("#dash-filter");
  const dashSearch = document.querySelector("#dash-search");
  if (dashFilter || dashSearch) {
    const apply = () => {
      const tipo = dashFilter?.value || "todos";
      const q = (dashSearch?.value || "").toLowerCase();
      document.querySelectorAll("[data-row]").forEach((row) => {
        const matchTipo = tipo === "todos" || row.dataset.tipo === tipo;
        const matchText = row.dataset.search.includes(q);
        row.style.display = matchTipo && matchText ? "" : "none";
      });
    };
    dashFilter?.addEventListener("change", apply);
    dashSearch?.addEventListener("input", apply);
  }

  if (tipoSelect) {
    toggleFields(tipoSelect.value);
    tipoSelect.addEventListener("change", () => {
      setTheme(tipoSelect.value);
      themeButtons.forEach((b) => b.classList.toggle("active", b.dataset.themeSet === tipoSelect.value));
      toggleFields(tipoSelect.value);
      updatePreview();
    });
  }

  form?.querySelectorAll("input, select, textarea").forEach((el) => {
    el.addEventListener("input", updatePreview);
    el.addEventListener("change", updatePreview);
  });
  updatePreview();

  form?.addEventListener("submit", () => {
    burstConfetti();
  });

  function setTheme(theme) {
    root.dataset.theme = theme;
    localStorage.setItem("megafest-theme", theme);
  }

  function toggleFields(tipo) {
    document.querySelectorAll("[data-show]").forEach((field) => {
      const show = field.dataset.show.split(" ").includes(tipo);
      field.classList.toggle("hidden-field", !show);
      field.querySelectorAll("input, select, textarea").forEach((input) => {
        input.disabled = !show;
      });
    });
  }

  function updatePreview() {
    if (!preview || !form) return;
    const data = Object.fromEntries(new FormData(form).entries());
    const labels = {
      evento: "Evento",
      deporte: "Deporte",
      aniversario: "Aniversario",
      festivo: "Día festivo",
    };
    preview.querySelector(".badge").textContent = labels[data.tipo] || "Celebración";
    preview.querySelector(".live-title").textContent = data.titulo || "Tu celebración en vivo";
    preview.querySelector(".live-desc").textContent =
      data.descripcion || "Completa el formulario y verás aquí cómo se siente la temática.";
    preview.querySelector(".live-meta").textContent = [
      data.fecha || "Fecha por definir",
      data.hora || "",
      data.lugar || "Lugar por confirmar",
    ]
      .filter(Boolean)
      .join(" · ");
    preview.style.setProperty("--accent", data.color || getComputedStyle(root).getPropertyValue("--accent"));
  }

  function burstConfetti() {
    const canvas = document.createElement("canvas");
    canvas.className = "confetti";
    canvas.width = innerWidth;
    canvas.height = innerHeight;
    document.body.appendChild(canvas);
    const ctx = canvas.getContext("2d");
    const bits = Array.from({ length: 80 }, () => ({
      x: Math.random() * canvas.width,
      y: -20,
      r: 4 + Math.random() * 6,
      c: ["#ff4fd8", "#22f0ff", "#ffe14a", "#22c55e", "#fb7185"][Math.floor(Math.random() * 5)],
      v: 3 + Math.random() * 6,
      s: Math.random() * 6,
    }));
    let frames = 0;
    const tick = () => {
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      bits.forEach((b) => {
        b.y += b.v;
        b.x += Math.sin((frames + b.s) / 8);
        ctx.fillStyle = b.c;
        ctx.fillRect(b.x, b.y, b.r, b.r * 1.6);
      });
      frames += 1;
      if (frames < 90) requestAnimationFrame(tick);
      else canvas.remove();
    };
    tick();
  }
})();
