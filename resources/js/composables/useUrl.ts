export function makeUrl(path: string) {
    return `${location.origin}/${path}`
}

export default function useUrl() {
    return { makeUrl }
}
