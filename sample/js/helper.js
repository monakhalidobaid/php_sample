// ==== Toast ====
function showToast(message, type='success'){
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.style.backgroundColor = (type==='success') ? '#4caf50':'#f44336';
    toast.className = 'toast show';
    toast.scrollIntoView({behavior:'smooth', block:'center'});
    setTimeout(()=>{ toast.className = toast.className.replace('show',''); }, 5000);
}

// debounce helper
function debounce(fn, delay = 350) {
  let timer; 
  return function(...args) {
    clearTimeout(timer);
    timer = setTimeout(() => fn.apply(this, args), delay);
  }
} 

function activateSidebarLink(selector = '.nav-link') {
    const currentPage = window.location.pathname.split('/').pop();
    document.querySelectorAll(selector).forEach(link => {
        const href = link.getAttribute('href');
        if (!href) return;
        const linkPage = href.split('/').pop();
        if (linkPage === currentPage) {
            link.classList.add('active');
        }
    });
}




document.addEventListener('DOMContentLoaded', () => activateSidebarLink());
