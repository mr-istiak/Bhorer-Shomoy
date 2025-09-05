import BanglaDate from 'bangla-date';

export function enToBnNumber(num: number | string): string {
    const en = ['0','1','2','3','4','5','6','7','8','9'];
    const bn = ['০','১','২','৩','৪','৫','৬','৭','৮','৯'];
    return String(num).split('').map(d => bn[en.indexOf(d)] ?? d).join('');
}

export const monthsEnToBn = [
    'জানুয়ারি','ফেব্রুয়ারি','মার্চ','এপ্রিল','মে','জুন',
    'জুলাই','আগস্ট','সেপ্টেম্বর','অক্টোবর','নভেম্বর','ডিসেম্বর'
];

export const weekdaysEnToBn: Record<string, string> = {
    Sunday: 'রবিবার',
    Monday: 'সোমবার',
    Tuesday: 'মঙ্গলবার',
    Wednesday: 'বুধবার',
    Thursday: 'বৃহস্পতিবার',
    Friday: 'শুক্রবার',
    Saturday: 'শনিবার'
};

export function getEnglishDateInBangla(date = new Date()) {
    const day = enToBnNumber(date.getDate());
    const month = monthsEnToBn[date.getMonth()];
    const year = enToBnNumber(date.getFullYear());
    return { day, month, year, join: () => `${day} ${month} ${year}` };
}

export function getBanglaWeekDay(date = new Date()) {
    return weekdaysEnToBn[date.toLocaleDateString('en-US', { weekday: 'long' })];
}

export function getGragorianToBongabdoDate(date:string | null = null) {
    const bdDate = date ? new BanglaDate(date) : new BanglaDate();
    return bdDate.toString();
}

export default { getEnglishDateInBangla, getGragorianToBongabdoDate, enToBnNumber, getBanglaWeekDay, monthsEnToBn, weekdaysEnToBn };

