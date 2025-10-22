import { slugify } from "#imports";
export interface TocItem {
  id: string;
  text: string;
  level: number;
}

export function buildToc(html: string): { toc: TocItem[]; html: string } {
  if (!html || typeof window === "undefined") return { toc: [], html: html || "" };

  const parser = new DOMParser();
  const doc = parser.parseFromString(html, "text/html");
  const headings = doc.querySelectorAll("h1, h2, h3, h4, h5, h6");

  const used = new Set<string>();
  const toc: TocItem[] = [];

  headings.forEach((el) => {
    const level = Number(el.tagName.substring(1));
    const text = (el.textContent || "").trim();
    if (!text) return;

    let id = el.id;
    if (!id) {
      id = slugify(text);
    }
    let i = 2;
    while (used.has(id)) id = `${id}-${i++}`;
    used.add(id);
    el.id = id;

    toc.push({ id, text, level });
  });

  const serialized = doc.body.innerHTML;
  return { toc, html: serialized };
}
