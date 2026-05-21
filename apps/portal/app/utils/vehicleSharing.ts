export interface VehicleShareAction {
    key: 'whatsapp' | 'x' | 'instagram' | 'email' | 'copy';
    labelKey: string;
    href?: string;
    copyText?: string;
}

export const buildVehicleWhatsAppMessage = (vehicleTitle: string): string => {
    return `Olá! Tenho interesse no veículo ${vehicleTitle} que vi no portal.`;
};

export const buildVehicleShareMessage = (vehicleTitle: string, vehicleUrl: string): string => {
    return `${vehicleTitle} - ${vehicleUrl}`;
};

export const buildVehicleWhatsAppUrl = (phone: string, message: string): string => {
    let cleanPhone = phone.replace(/\D/g, '');

    if (cleanPhone.startsWith('55')) {
        cleanPhone = cleanPhone.slice(2);
    }

    const encodedMessage = encodeURIComponent(message);

    return `https://wa.me/55${cleanPhone}?text=${encodedMessage}`;
};

export const buildVehicleShareActions = (vehicleTitle: string, vehicleUrl: string): VehicleShareAction[] => {
    const message = buildVehicleShareMessage(vehicleTitle, vehicleUrl);
    const encodedMessage = encodeURIComponent(message);

    return [
        {
            key: 'whatsapp',
            labelKey: 'sharing.whatsapp',
            href: `https://wa.me/?text=${encodedMessage}`,
        },
        {
            key: 'x',
            labelKey: 'sharing.x',
            href: `https://x.com/intent/post?text=${encodedMessage}`,
        },
        {
            key: 'instagram',
            labelKey: 'sharing.instagram',
            copyText: message,
        },
        {
            key: 'email',
            labelKey: 'sharing.email',
            href: `mailto:?subject=${encodeURIComponent(vehicleTitle)}&body=${encodedMessage}`,
        },
        {
            key: 'copy',
            labelKey: 'sharing.copy',
            copyText: `${vehicleTitle}\n${vehicleUrl}`,
        },
    ];
};
