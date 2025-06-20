// JS para index.html - Portafolio Emanuel Torres

// Cargar categorías y usuarios para los filtros
function cargarFiltros() {
    fetch('api/categorias.php')
        .then(res => res.json())
        .then(data => {
            if (data.success && Array.isArray(data.categorias)) {
                const select = document.getElementById('filtro-categoria');
                data.categorias.forEach(cat => {
                    const opt = document.createElement('option');
                    opt.value = cat.id;
                    opt.textContent = cat.nombre;
                    select.appendChild(opt);
                });
            }
        });
    fetch('api/usuarios.php')
        .then(res => res.json())
        .then(data => {
            if (data.success && Array.isArray(data.usuarios)) {
                const select = document.getElementById('filtro-usuario');
                data.usuarios.forEach(user => {
                    const opt = document.createElement('option');
                    opt.value = user.id;
                    opt.textContent = user.nombre || user.email;
                    select.appendChild(opt);
                });
            }
        });
}

function cargarProyectos() {
    const categoria = document.getElementById('filtro-categoria').value;
    const usuario = document.getElementById('filtro-usuario').value;
    let url = 'api/proyectos.php';
    const params = [];
    if (categoria) params.push('categoria_id=' + encodeURIComponent(categoria));
    if (usuario) params.push('usuario_id=' + encodeURIComponent(usuario));
    if (params.length) url += '?' + params.join('&');
    fetch(url)
        .then(res => res.json())
        .then(data => {
            const cont = document.getElementById('proyectos-list');
            if(Array.isArray(data)) {
                // Filtrar solo proyectos visibles de forma robusta
                const visibles = data.filter(p => {
                    return p.visible == 1 || p.visible === "1" || p.visible === true;
                });
                // Ordenar primero los destacados
                visibles.sort((a, b) => {
                    const da = (a.destacado == 1 || a.destacado === "1" || a.destacado === true) ? 1 : 0;
                    const db = (b.destacado == 1 || b.destacado === "1" || b.destacado === true) ? 1 : 0;
                    return db - da;
                });
                cont.innerHTML = visibles.map(p => `
                    <div class="proyecto-card">
                        <h3>${p.titulo} ${(p.destacado == 1 || p.destacado === "1" || p.destacado === true) ? '⭐' : ''}</h3>
                        <p>${p.descripcion_corta || p.descripcion}</p>
                        ${p.descripcion_corta && p.descripcion && p.descripcion_corta !== p.descripcion ? `<button class="ver-mas-btn" onclick="mostrarDescripcion(this)">Ver más</button><div class="descripcion-completa" style="display:none;">${p.descripcion}</div>` : ''}
                        ${p.imagen_principal ? `<img src="uploads/${p.imagen_principal}" style="max-width:200px;">` : ''}
                        ${(() => {
                            if (!p.tecnologias) return '';
                            try {
                                const tech = JSON.parse(p.tecnologias);
                                if (Array.isArray(tech)) {
                                    return `<p><b>Tecnologías:</b> ${tech.join(', ')}</p>`;
                                } else if (typeof tech === 'string') {
                                    return `<p><b>Tecnologías:</b> ${tech}</p>`;
                                }
                                return '';
                            } catch (e) {
                                return `<p><b>Tecnologías:</b> ${p.tecnologias}</p>`;
                            }
                        })()}
                        ${p.url_demo ? `<p><a href="${p.url_demo}" target="_blank">Ver Demo</a></p>` : ''}
                        ${p.url_repositorio ? `<p><a href="${p.url_repositorio}" target="_blank">Repositorio</a></p>` : ''}
                        ${(p.fecha_inicio || p.fecha_fin) ? `<p><b>Fechas:</b> ${p.fecha_inicio || ''}${p.fecha_fin ? ' - ' + p.fecha_fin : ''}</p>` : ''}
                        <p><b>Autor:</b> ${p.autor ? p.autor : 'Desconocido'}</p>
                    </div>
                `).join('');
            } else {
                cont.innerHTML = '<p>No hay proyectos disponibles.</p>';
            }
            mostrarMensajeProyectosVacio(); // Llama a la función para mostrar/ocultar mensaje
        });
}

// Mostrar mensaje si no hay proyectos
function mostrarMensajeProyectosVacio() {
    const lista = document.getElementById('proyectos-list');
    const vacio = document.getElementById('proyectos-vacio');
    if (lista && vacio) {
        if (!lista.hasChildNodes() || lista.children.length === 0) {
            vacio.style.display = 'block';
        } else {
            vacio.style.display = 'none';
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    cargarFiltros();
    cargarProyectos();
    document.getElementById('filtro-categoria').addEventListener('change', cargarProyectos);
    document.getElementById('filtro-usuario').addEventListener('change', cargarProyectos);
});

function mostrarDescripcion(btn) {
    const desc = btn.nextElementSibling;
    if (desc.style.display === 'none' || desc.style.display === '') {
        desc.style.display = 'block';
        btn.textContent = 'Ocultar';
    } else {
        desc.style.display = 'none';
        btn.textContent = 'Ver más';
    }
}

// Lightbox para imágenes
document.addEventListener('click', function(e) {
    if (e.target.matches('.proyecto-card img')) {
        const src = e.target.getAttribute('src');
        const lightboxBg = document.createElement('div');
        lightboxBg.className = 'proyecto-lightbox-bg';
        const img = document.createElement('img');
        img.className = 'proyecto-lightbox-img';
        img.src = src;
        lightboxBg.appendChild(img);
        document.body.appendChild(lightboxBg);
        lightboxBg.addEventListener('click', function() {
            document.body.removeChild(lightboxBg);
        });
    }
});

// Funcionalidad del menú móvil
const menuToggle = document.getElementById('menu-toggle');
const navLinks = document.getElementById('nav-links');
if (menuToggle && navLinks) {
    menuToggle.addEventListener('click', function() {
        navLinks.classList.toggle('open');
    });
    document.querySelectorAll('.nav-links a').forEach(function(link) {
        link.addEventListener('click', function() {
            navLinks.classList.remove('open');
        });
    });
}

document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        if (href.startsWith('#')) {
            const target = document.querySelector(href);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    });
});
