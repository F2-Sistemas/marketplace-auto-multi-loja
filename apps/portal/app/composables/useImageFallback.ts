import useMockery from './useMockery';

export function useImageFallback() {
    const { getPlaceholderImage } = useMockery();

    const handleImageError = (event: Event, text = 'Sem Imagem') => {
        const target = event.target as HTMLImageElement;
        if (target && !target.dataset.hasFailed) {
            target.dataset.hasFailed = 'true';
            target.src = getPlaceholderImage(600, 400, '31343C', 'EEE', text);
        }
    };

    return {
        getPlaceholderImage,
        handleImageError,
    };
}
