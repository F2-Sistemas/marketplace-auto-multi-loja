import { describe, expect, it } from 'vitest';
import {
    buildVehicleShareActions,
    buildVehicleShareMessage,
    buildVehicleWhatsAppMessage,
    buildVehicleWhatsAppUrl,
} from '../../app/utils/vehicleSharing';

describe('vehicleSharing', () => {
    it('builds the default whatsapp message', () => {
        expect(buildVehicleWhatsAppMessage('Hyundai Creta Sport TSI')).toBe(
            'Olá! Tenho interesse no veículo Hyundai Creta Sport TSI que vi no portal.'
        );
    });

    it('builds a whatsapp url with sanitized phone and encoded message', () => {
        const url = buildVehicleWhatsAppUrl('(84) 98888-8888', 'Olá! Tenho interesse.');

        expect(url).toBe('https://wa.me/5584988888888?text=Ol%C3%A1!%20Tenho%20interesse.');
    });

    it('builds all share actions expected by the share block', () => {
        const actions = buildVehicleShareActions(
            'Chevrolet Cruze',
            'http://localhost:3000/veiculos/chevrolet-cruze-aut-couro-928'
        );

        expect(actions.map((action) => action.key)).toEqual([
            'whatsapp',
            'x',
            'instagram',
            'email',
            'copy',
        ]);
        expect(actions.map((action) => action.labelKey)).toEqual([
            'sharing.whatsapp',
            'sharing.x',
            'sharing.instagram',
            'sharing.email',
            'sharing.copy',
        ]);
        expect(actions[0].href).toContain('wa.me');
        expect(actions[1].href).toContain('x.com/intent/post');
        expect(actions[2].copyText).toBe(
            'Chevrolet Cruze - http://localhost:3000/veiculos/chevrolet-cruze-aut-couro-928'
        );
        expect(actions[3].href).toContain('mailto:');
        expect(actions[4].copyText).toContain('Chevrolet Cruze');
    });

    it('builds the share message using title and url', () => {
        expect(
            buildVehicleShareMessage(
                'Chevrolet Cruze',
                'http://localhost:3000/veiculos/chevrolet-cruze-aut-couro-928'
            )
        ).toBe('Chevrolet Cruze - http://localhost:3000/veiculos/chevrolet-cruze-aut-couro-928');
    });
});
