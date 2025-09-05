import axios from "axios";

export function doesContainBanglaText(text : string) : boolean
{
    return /[\u0980-\u09FF]/.test(text);
}

export async function translateToEn(source:string)
{  
    if(!doesContainBanglaText(source)) return null;
    const response = await axios.post(route('translate', { fromLang: 'bn', toLang: 'en' }), { source });
    return response.data
}

export default function useTranslation() {
    return { translateToEn, doesContainBanglaText }
}