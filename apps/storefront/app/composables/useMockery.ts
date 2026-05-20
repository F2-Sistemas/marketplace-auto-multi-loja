/*
- Images:
    - To use mockup image, use https://placehold.co like:
        - https://placehold.co/600x400/EEE/31343C <!-- gray image -->
        - https://placehold.co/600x400/000000/FFF <!-- black image -->
        - https://placehold.co/600x400?text=Hello+World <!-- With text -->
        ... more example in https://placehold.co/
*/

export default function () {
    return {
        getPlaceholderImage: (width = 600, height = 400, bgColor = 'EEE', textColor = '31343C', text = ''): string => {
            let url = `https://placehold.co/${width}x${height}/${bgColor}/${textColor}`;
            if (text) {
                const encodedText = encodeURIComponent(text);
                url += `?text=${encodedText}`;
            }
            return url;
        },
    };
}
