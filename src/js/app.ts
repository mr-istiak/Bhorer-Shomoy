import pjax from '@/pjax'
import { createApp } from 'petite-vue'
import { getEnglishDateInBangla, getGragorianToBongabdoDate, getBanglaWeekDay, enToBnNumber } from '@/date';

interface BoundingBox {
    x: number;
    y: number;
    width: number;
    height: number;
}

const linker = pjax({
    container: '#app',
    progress: '#progressbar'
})
const appConfig = {
    getEnglishDateInBangla,
    getBanglaWeekDay,
    getGragorianToBongabdoDate,
    enToBnNumber,
    loadUrl: linker.load,
    pdfToImgRect: (rectPDF : BoundingBox, dpi : number, imageHeight: number, extendingScale = 1) : BoundingBox => {
        const scale = (dpi / 72) * extendingScale; // points -> pixels
        return {
            x: rectPDF.x * scale,
            y: imageHeight - (rectPDF.y  + rectPDF.height) * scale, // flip Y
            width: rectPDF.width * scale,
            height: rectPDF.height * scale,
        };
    }
}
linker.start(createApp(appConfig));
createApp(appConfig).mount('header')