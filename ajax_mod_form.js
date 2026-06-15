document.addEventListener('DOMContentLoaded', function () {
    const cursoSelect = document.getElementById('id_curso');
    const disciplinaSelect = document.getElementById('id_disciplina');

    if (!cursoSelect || !disciplinaSelect || typeof allDisciplinas === 'undefined') return;

    // Cria mapa
    const disciplinaMap = {};
    allDisciplinas.forEach(d => {
        disciplinaMap[d.id_disciplina_instituicao] = d.id_curso_instituicao;
    });

    function filtrarDisciplinas(cursoid) {
        const currentValue = disciplinaSelect.value;
        const placeholder = disciplinaSelect.querySelector('option[value=""]');
        disciplinaSelect.innerHTML = '';
        if (placeholder) {
            disciplinaSelect.appendChild(placeholder);
        }

        allDisciplinas.forEach(d => {
            if (!cursoid || d.id_curso_instituicao == cursoid) {
                const option = document.createElement('option');
                option.value = d.id_disciplina_instituicao;
                option.textContent = d.nome_disciplina;
                disciplinaSelect.appendChild(option);
            }
        });

        // Restaura a seleção
        disciplinaSelect.value = currentValue;
    }

    cursoSelect.addEventListener('change', function () {
        disciplinaSelect.value = ''; // Limpa a seleção ao mudar curso
        // Limpa o display do autocomplete da disciplina manualmente
        const selectionDivs = document.querySelectorAll('.form-autocomplete-selection');
        const selectionDiv = selectionDivs[1]; // Assume disciplina é o segundo autocomplete
        if (selectionDiv) {
            selectionDiv.innerHTML = '<span class="m-1 h-5" id="">Selecione</span>';
            selectionDiv.setAttribute('data-active-value', '');
        }
        filtrarDisciplinas(this.value);
    });

    // Filtra inicialmente
    filtrarDisciplinas(cursoSelect.value);
});
