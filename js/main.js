// Arquivo main.js da Rede Colmeia

console.log("JavaScript da Rede Colmeia carregado com sucesso!");

// --- CÓDIGO DO MENU MOBILE (MANTIDO) ---
const menuToggleButton = document.querySelector('.menu-toggle');
const navLinks = document.querySelector('header nav ul');

if (menuToggleButton && navLinks) {
    menuToggleButton.addEventListener('click', () => {
        navLinks.classList.toggle('active');
        menuToggleButton.classList.toggle('active');
    });
}

// ===================================================================
// === CÓDIGO AVANÇADO DO CURSO SEQUENCIAL ===
// ===================================================================

if (document.querySelector('.course-container')) {
    const playerFrame = document.getElementById('video-player');
    const moduleItems = document.querySelectorAll('.module-item');
    const currentTitle = document.getElementById('current-title');
    const completeButton = document.getElementById('complete-button');
    const certificadoArea = document.getElementById('certificado-area');
    
    let currentModuleIndex = 0;
    const TOTAL_MODULES = moduleItems.length;

    // --- FUNÇÕES DE CONTROLE ---
    
    // Função para carregar um vídeo no player principal
    function loadVideo(index) {
        const item = moduleItems[index];
        
        // Impede que aulas bloqueadas sejam carregadas (exceto a primeira)
        if (item.classList.contains('locked') && index !== 0) {
            alert("Esta aula está bloqueada! Conclua a anterior para continuar.");
            return;
        }

        const videoId = item.dataset.videoId;
        const start = item.dataset.start;
        const title = item.dataset.title;
        
        // Atualiza a URL do iframe
        const finalVideoId = (typeof COURSE_VIDEO_ID !== 'undefined') ? COURSE_VIDEO_ID : 'VIDEO_ID_HERE';
        playerFrame.src = `https://www.youtube.com/embed/${finalVideoId}?enablejsapi=1&start=${start}`;
        
        // Atualiza o título do vídeo
        currentTitle.textContent = title;
        
        // 1. Remove o estado 'active' de todos
        moduleItems.forEach(li => li.classList.remove('active'));
        
        // 2. Marca o novo módulo como ativo
        item.classList.add('active');
        currentModuleIndex = index;

        // 3. Configura o botão de progressão
        if (index < TOTAL_MODULES - 1) {
            completeButton.style.display = 'block';
            completeButton.textContent = 'Marcar como Concluída e Ir para Próxima Aula';
        } else {
            // Se for o último módulo
            if(item.classList.contains('completed')) {
                // Se já foi concluída, o botão some e mostra o certificado
                showCertificate();
                completeButton.style.display = 'none';
            } else {
                completeButton.style.display = 'block';
                completeButton.textContent = 'Marcar como Concluída e Finalizar Curso';
            }
        }
        
        // 4. Garante que o certificado esteja escondido
        certificadoArea.classList.add('hidden');
    }
    
    // Função para desbloquear o próximo módulo (chamado pelo botão)
    function unlockNextModule() {
        if (currentModuleIndex < TOTAL_MODULES) {
            // 1. Marca o módulo atual como concluído
            const currentModule = moduleItems[currentModuleIndex];
            currentModule.classList.add('completed');
            currentModule.classList.remove('active');
            currentModule.querySelector('.status-icon').innerHTML = '<i class="fas fa-check-circle"></i>';

            // 2. Se não for o último módulo, desbloqueia e carrega o próximo
            if (currentModuleIndex < TOTAL_MODULES - 1) {
                const nextIndex = currentModuleIndex + 1;
                const nextModule = moduleItems[nextIndex];
                
                // Desbloqueia e remove o bloqueio de clique
                nextModule.classList.remove('locked');
                nextModule.classList.add('unlocked');
                nextModule.querySelector('.status-icon').innerHTML = '<i class="fas fa-play-circle"></i>';
                
                // Habilita o clique e carrega o próximo módulo
                nextModule.addEventListener('click', () => loadVideo(nextIndex));
                loadVideo(nextIndex); 
            } else {
                // 3. Se for o último módulo, MOSTRA O CERTIFICADO
                showCertificate();
                completeButton.style.display = 'none'; // Esconde o botão de conclusão final
            }
        }
    }
    
    // Função para mostrar a área de certificado
    function showCertificate() {
        certificadoArea.classList.remove('hidden');
        document.getElementById('workshop-title').scrollIntoView({ behavior: 'smooth' });
    }

    // --- SETUP INICIAL ---

    // Configura os listeners de clique para as aulas (só na primeira, as outras são habilitadas em unlockNextModule)
    moduleItems.forEach((item, index) => {
        if (index === 0) {
            item.addEventListener('click', () => loadVideo(index));
        }
    });

    // Listener para o botão de Conclusão Manual
    if (completeButton) {
        completeButton.addEventListener('click', unlockNextModule);
    }
    
    // Carrega o primeiro vídeo
    loadVideo(0); 
}

// === LÓGICA DO MODAL DE IMAGEM ===

function openModal(src) {
    const modal = document.getElementById("imageModal");
    const modalImg = document.getElementById("img01");
    
    modal.style.display = "flex"; // Usa flex para centralizar perfeitamente
    modal.style.alignItems = "center";
    modal.style.justifyContent = "center";
    
    modalImg.src = src;
}

function closeModal() {
    document.getElementById("imageModal").style.display = "none";
}