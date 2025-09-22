 // Roles disponibles
  const rolesRemite = ["ESTUDIANTE", "EMPLEADO", "DIRECTIVO", "DOCENTE", "ACUDIENTE"];
  const rolesRemitido = ["ESTUDIANTE", "EMPLEADO", "DIRECTIVO", "DOCENTE"];

  // Opciones para otros selects
  const programas = ["1", "2", "3", "4", "5"];
  const horarios = ["Mañana", "Tarde", "Noche"];
  const semestres = ["1", "2", "3", "4"];

  /**
   * Llena un <select> con opciones de roles
   * @param {string} id - ID del select
   * @param {string[]} rolesArray - Array de roles
   */
  function llenarSelect(id, rolesArray) {
    const select = document.getElementById(id);
    if (!select) return;
    rolesArray.forEach(rol => {
      const option = document.createElement("option");
      option.textContent = rol;
      select.appendChild(option);
    });
  }

  llenarSelect("rol", rolesRemite);
  llenarSelect("rolRemitido", rolesRemitido);

  // Llenar selects de programa, horario y semestre
  llenarSelect("programa", programas);
  llenarSelect("horario", horarios);
  llenarSelect("semestre", semestres);

  /**
   * Valida los campos obligatorios del formulario
   * @param {HTMLFormElement} form
   * @returns {boolean}
   */
  function validarFormulario(form) {
    let valido = true;
    let mensaje = "";
    // Validación básica de campos requeridos
    [
      { id: "nombre", label: "Nombre de quien remite" },
      { id: "rol", label: "Rol de quien remite" },
      { id: "nombreRemitido", label: "Nombre del remitido" },
      { id: "rolRemitido", label: "Rol del remitido" },
      { id: "programa", label: "Programa" },
      { id: "semestre", label: "Semestre" },
      { id: "horario", label: "Horario" },
      { id: "correo_remitido", label: "Correo del remitido" },
      { id: "telefonoRemitido", label: "Teléfono del remitido" },
      { id: "telefonoRemite", label: "Teléfono de quien remite" }
    ].forEach(campo => {
      const el = form.elements[campo.id];
      if (!el || !el.value || el.value === "") {
        valido = false;
        mensaje += `- ${campo.label}\n`;
      }
    });
    if (!valido) {
      mostrarAlerta("Por favor completa los siguientes campos obligatorios:\n" + mensaje, "bg-red-100 text-red-700");
    }
    return valido;
  }

  // Manejo del envío con fetch y validación robusta
  document.getElementById("remisionForm").addEventListener("submit", async (e) => {
    e.preventDefault();
    const form = e.target;

    // Validación previa
    if (!validarFormulario(form)) return;

    const formData = new FormData(form);
    try {
      const response = await fetch("src/guardar_estudiante.php", {
        method: "POST",
        body: formData
      });
      const result = await response.text();
      if (response.ok && result.trim() === "OK") {
        mostrarAlerta("Registro guardado correctamente", "bg-green-100 text-green-700");
        form.reset();
      } else {
        mostrarAlerta("Error al guardar el registro: " + result, "bg-red-100 text-red-700");
      }
    } catch (error) {
      mostrarAlerta("Error de red o del servidor. Intenta nuevamente.", "bg-red-100 text-red-700");
      console.error("Error al enviar el formulario:", error);
    }
  });

  /**
   * Muestra una alerta en pantalla
   * @param {string} mensaje
   * @param {string} clases
   */
  function mostrarAlerta(mensaje, clases) {
    const alerta = document.getElementById("alerta");
    if (!alerta) return;
    alerta.textContent = mensaje;
    alerta.className = `p-3 mb-4 rounded text-center font-semibold ${clases}`;
    alerta.classList.remove("hidden");
    // Ocultar automáticamente a los 4 segundos
    setTimeout(() => {
      alerta.classList.add("hidden");
    }, 4000);
  }

  // Desactivar autocompletado en todos los campos
  window.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll("input, textarea, select").forEach(el => el.setAttribute("autocomplete", "off"));
  });