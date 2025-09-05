import { BoundingBox } from "@/types";
import bnBijoy2Unicode from "@codesigntheory/bnbijoy2unicode";

export type TextFragment = {
    text: string;
    fontSizeCSS: number;
}

export type Image = {
    image: any;
    rectPdf: BoundingBox;
}
export function isBijoy(text: string) {
    for (const char of text) {
        const code = char.charCodeAt(0);
        if (code >= 128 && code <= 255) return true;
    }
    return false;
}


/**
 * Given a bounding box, returns a new bounding box with
 * the left and top edges moved to the minimum of the
 * original x and x + width, and the minimum of the
 * original y and y + height, respectively. The width
 * and height are set to the absolute values of the
 * original width and height.
 *
 * This is useful for normalizing bounding boxes that
 * may have negative widths or heights due to the
 * order in which their edges were defined.
 *
 * @param rect The original bounding box.
 *
 * @returns A new bounding box with the edges normalized.
 */
export function normalizeRect(rect: BoundingBox) : BoundingBox
{
    const x = Math.min(rect.x, rect.x + rect.width);
    const y = Math.min(rect.y, rect.y + rect.height);
    const width = Math.abs(rect.width);
    const height = Math.abs(rect.height);
    return { x, y, width, height };
}

/**
 * Convert a bounding box in screen coordinates to PDF coordinates.
 *
 * @param rectScreen The bounding box in screen coordinates, as a
 * BoundingBox object.
 * @param scale The current scale of the PDF, as a number.
 * @param canvas The HTML canvas element that the PDF is rendered
 * into, as an HTMLCanvasElement.
 *
 * @returns A new bounding box object with the coordinates converted
 * to PDF coordinates.
 */
export function screenToPdfCoords(rectScreen: BoundingBox, scale: number, canvas: HTMLCanvasElement) : BoundingBox
{
    return {
        x: rectScreen.x / scale,
        y: (canvas.height - (rectScreen.y + rectScreen.height)) / scale, // PDF origin bottom-left
        width: rectScreen.width / scale,
        height: rectScreen.height / scale
    };
}

export function pdfToScreenCoords(rectPdf: BoundingBox, scale: number, canvas: HTMLCanvasElement): BoundingBox {
    return {
        x: rectPdf.x * scale,
        // invert y because PDF origin is bottom-left but canvas origin is top-left
        y: canvas.height - (rectPdf.y + rectPdf.height) * scale,
        width: rectPdf.width * scale,
        height: rectPdf.height * scale
    };
}

/**
 * A wrapper around screenToPdfCoords that first normalizes the
 * bounding box to ensure that its width and height are positive.
 * This is useful when the bounding box may have been defined with
 * negative widths or heights, due to the order in which its edges
 * were defined.
 *
 * @param rectScreen The bounding box in screen coordinates, as a
 * BoundingBox object.
 * @param scale The current scale of the PDF, as a number.
 * @param canvas The HTML canvas element that the PDF is rendered
 * into, as an HTMLCanvasElement.
 *
 * @returns A new bounding box object with the coordinates converted
 * to PDF coordinates.
 */
export function screenToPdfCoordsN(rectScreen: BoundingBox, scale: number, canvas: HTMLCanvasElement) : BoundingBox
{
    return screenToPdfCoords(normalizeRect(rectScreen), scale, canvas);
}

/**
 * Check if two rectangles intersect.
 * Both r1, r2 in same coordinate system
 * @param r1 The first rectangle, as {x, y, width, height}
 * @param r2 The second rectangle, as {x, y, width, height}
 * @returns true if the rectangles intersect, false otherwise
 */
export function intersects(r1:any, r2:any) { 
    return !( r2.x > r1.x + r1.width || r2.x + r2.width < r1.x || r2.y > r1.y + r1.height || r2.y + r2.height < r1.y ); 
}

/**
 * Given a PDF page and a bounding box in PDF coordinates, extract
 * all text fragments whose bounding box intersects the given
 * bounding box.
 * 
 * @param rectPdf The bounding box in PDF coordinates, as a
 * BoundingBox object.
 * @param page The PDF.js page object.
 * @param viewport The viewport object from the PDF.js page.
 * 
 * @returns An array of TextFragment objects, each containing the
 * text of the fragment, its font size, and its bounding box in
 * PDF coordinates.
 */
export async function extractTextFromSelection(rectPdf: BoundingBox, page: any) : Promise<TextFragment[]>
{
    if (!page) return [];
    const textContent = await page.getTextContent();
    const selected: TextFragment[] = [];

    for (const item of textContent.items) {
        const [a, b, c, d, e, f] = item.transform;
        const itemX = e;
        const itemY = f;
        const itemWidth = item.width || 0;
        const itemHeight = d || 0;

        const itemRectPdf = normalizeRect({ x: itemX, y: itemY - itemHeight, width: itemWidth, height: itemHeight });
        // simple bounding box intersection
        if (intersects(rectPdf, itemRectPdf)) {
            const fontSize = Math.sqrt(b*b + d*d);
            selected.push({ 
                text: bnBijoy2Unicode(item.str), 
                fontSizeCSS: fontSize
            });
        }
    }
    return selected;
}

function getHeadingForFontSize(fontSize: number) : string | null
{
        // Example ranges: you can tweak
        if (fontSize > 25) return "h1";
        if (fontSize > 18) return "h2";
        if (fontSize > 14) return "h3";
        return null
    };

function isValidText(char: string) {
    const code = char.charCodeAt(0);

    // Ignore "private use" symbols often used as placeholders
    // Unicode ranges:
    // - Basic Latin: 0x20 - 0x7E
    // - Latin-1 Supplement: 0xA0 - 0xFF
    // - Bangla: 0x0980 - 0x09FF
    // Add more if needed

    // Skip control chars and placeholders
    if (
        (code >= 0x20 && code <= 0x7E) ||   // Latin letters
        (code >= 0xA0 && code <= 0xFF) ||   // Latin-1 Supplement / Bijoy ASCII
        (code >= 0x0980 && code <= 0x09FF)  // Bangla Unicode
    ) {
        return true;
    }

    return false; // ignore others like 
}

/**
 * Sorts text fragments and images by vertical position (PDF Y) and
 * generates an array of strings and images, where strings are
 * wrapped in HTML tags corresponding to their font size, and images
 * are unchanged.
 *
 * @param textFragments - an array of TextFragments extracted from a PDF
 * @param images - an array of Images extracted from a PDF
 * @returns an array of strings and images, where strings are wrapped in
 * HTML tags and images are unchanged
 */
export function transformTextToHtml(textFragments: TextFragment[]): string
{
    let html:string = "";
    for (const fragment of textFragments) { 
        let text = fragment.text.trim();
        if(text && isValidText(text) && (text.length > 0) && (!text.trim().endsWith(' পাতায় দেখুন')) ) {
            if(text.includes('নিজস্ব প্রতিবেদক') || text.includes('বিশেষ প্রতিবেদক')) {
                html += ` <b>${fragment.text}</b>`;
                continue;
            }
            const heading = getHeadingForFontSize(fragment.fontSizeCSS);
            if(heading) {
                html += `<${heading}>${fragment.text}</${heading}>`;
            } else html +=` ${fragment.text}`
        }
    }
    return html;
}

export default { extractTextFromSelection, intersects, normalizeRect, screenToPdfCoords, screenToPdfCoordsN, transformTextToHtml, isBijoy }