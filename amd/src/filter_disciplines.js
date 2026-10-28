// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Filter disciplines based on selected course.
 *
 * @module mod_intersaberesplay/filter_disciplines
 * @copyright 2026 Editora Intersaberes
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('mod_intersaberesplay/filter_disciplines', [], function() {
    'use strict';

    return {
        init: function(allDisciplines) {
                const courseSelect = document.getElementById('id_curso');
                const disciplineSelect = document.getElementById('id_disciplina');

                if (!courseSelect || !disciplineSelect || !Array.isArray(allDisciplines)) {
                    return;
                }

                /**
                 * Filter disciplines based on course selection.
                 *
                 * @param {number|string} courseId The selected course ID.
                 */
                const filterDisciplines = function(courseId) {
                    const currentValue = disciplineSelect.value;
                    const placeholder = disciplineSelect.querySelector('option[value=""]');

                    disciplineSelect.innerHTML = '';
                    if (placeholder) {
                        disciplineSelect.appendChild(placeholder);
                    }

                    allDisciplines.forEach(function(discipline) {
                        if (!courseId || String(discipline.id_curso_instituicao) === String(courseId)) {
                            const option = document.createElement('option');
                            option.value = discipline.id_disciplina_instituicao;
                            option.textContent = discipline.nome_disciplina;
                            disciplineSelect.appendChild(option);
                        }
                    });

                    if (currentValue) {
                        const currentOption = Array.from(disciplineSelect.options).find(function(opt) {
                            return opt.value === currentValue;
                        });
                        if (currentOption) {
                            disciplineSelect.value = currentValue;
                        }
                    }
                };

                filterDisciplines(courseSelect.value);
                courseSelect.addEventListener('change', function() {
                    filterDisciplines(this.value);
                });


        }
    };
});