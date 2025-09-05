interface Config {
    container?: string;
    progress?: string;
}

function startProgress(progress: HTMLElement): number 
{
  progress.classList.remove('w-0');
  progress.style.width = '0%';
  progress.style.opacity = '1';

  let width = 0;

  return window.setInterval(() => {
    if (width < 90) {
      width += Math.random() * 5; // random increment
      progress.style.width = width + '%';
    }
  }, 100);
}

function finishProgress(progress: HTMLElement): void 
{
  progress.style.width = '100%';
  setTimeout(() => {
    progress.style.opacity = '0';
    progress.style.width = '0';
  }, 200);
}

export async function load(url: string, containerSelector: string): Promise<{ res: Response, html: string, doc: Document, newContent: HTMLElement }>
{
  const res = await fetch(url);
  const html = await res.text();
  const parser = new DOMParser();
  const doc = parser.parseFromString(html, 'text/html');
  const newContent = doc.querySelector<HTMLElement>(containerSelector);
  if (!newContent) {
    throw new Error('Container not found');
  }
  return { res, html, doc, newContent };
}

export async function loadPage(url: string, container: HTMLElement, progress: HTMLElement, containerSelector: string, app:any = null): Promise<void> 
{
  if(app) app.unmount();
  container.classList.add('opacity-0'); // fade out

  const progressInterval = startProgress(progress);

  try {
    const { newContent } = await load(url, containerSelector);

    if (newContent) {
      clearInterval(progressInterval);
      container.innerHTML = newContent.innerHTML;
      container.classList.remove('opacity-0');
      container.className = newContent.className;
      finishProgress(progress);
      history.pushState(null, '', url);
      if (app) app.mount(containerSelector);
    } else {
      window.location.href = url; // fallback
    }
  } catch (err) {
    clearInterval(progressInterval);
    finishProgress(progress);
    console.error('PJAX error:', err);
    window.location.href = url; // fallback
  }
}

export default function pjax(config: Config = {}) 
{
    const containerEl = document.querySelector<HTMLElement>(config.container || '#pjax-container')!;
    const progressEl = document.querySelector<HTMLElement>(config.progress || '#pjax-progress')!;
    return {
        load,
        loadPage,
        start: (app:any = null) => {
            if(app) app.mount(config.container || '#pjax-container');
            document.addEventListener('click', (e: MouseEvent) => {
                const target = e.target as HTMLElement;
                const link = target.closest<HTMLAnchorElement>('a');
                if (!link) return;
                if (link.target === '_blank') return;
                if (link.origin !== location.origin) return;

                e.preventDefault();
                loadPage(link.href, containerEl, progressEl, config.container || '#pjax-container', app);
            });
                // Handle back/forward
            window.addEventListener('popstate', () => {
                loadPage(location.href, containerEl, progressEl, config.container || '#pjax-container', app);
            });
        }
    }
}