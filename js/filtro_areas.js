document.addEventListener('DOMContentLoaded', () => {
    const btnToggle = document.getElementById('btnToggleAreas');
    const areaOptions = document.getElementById('areaOptions');
    const selectedTagsContainer = document.getElementById('selectedAreasTags');
    const hiddenInput = document.getElementById('areasInput');

    // Toggle dropdown
    btnToggle.addEventListener('click', e => {
        e.stopPropagation();
        areaOptions.style.display = areaOptions.style.display === 'block' ? 'none' : 'block';
    });

    // Atualiza tags
    function atualizarTags(){
        selectedTagsContainer.innerHTML = '';
        const checkboxes = document.querySelectorAll('.chkArea');
        const selecionadas = [];

        checkboxes.forEach(chk => {
            if(chk.checked){
                selecionadas.push(chk.value);
                const labelText = chk.closest('label').querySelector('.area-label').textContent.trim();
                const tag = document.createElement('span');
                tag.classList.add('tag');
                tag.innerHTML = `${labelText} <span class='remove-tag'>&times;</span>`;
                tag.querySelector('.remove-tag').addEventListener('click', ev=>{
                    ev.stopPropagation();
                    chk.checked = false;
                    atualizarTags();
                });
                selectedTagsContainer.appendChild(tag);
            }
        });

        hiddenInput.value = selecionadas.join(',');
    }

    areaOptions.addEventListener('change', atualizarTags);

    document.addEventListener('click', e=>{
        if(!e.target.closest('#areaOptions') && !e.target.closest('#btnToggleAreas')){
            areaOptions.style.display = 'none';
        }
    });

    atualizarTags();
});
