/**
 * Hardware Catalog Data
 *
 * STRUCTURE (ready for manufacturer data):
 *   brands → categories → products → variants → compatibility_rules
 *
 * Current state: generic handle types only. Real hardware data
 * (Blum, Hettich, Hafele, etc.) will populate this when confirmed.
 *
 * Rule: "available_at_lc" must be true ONLY for hardware the LC actually uses.
 */
export const HARDWARE_BRANDS = [
    {
        id: 'generic',
        name: 'Padrão LC',
        available_at_lc: true,
        description: 'Puxadores e ferragens padrão oferecidas pela LC',
    },
    { id: 'blum', name: 'Blum', available_at_lc: false, description: 'Aguardando confirmação' },
    { id: 'hettich', name: 'Hettich', available_at_lc: false, description: 'Aguardando confirmação' },
    { id: 'hafele', name: 'Häfele', available_at_lc: false, description: 'Aguardando confirmação' },
    { id: 'fgvtn', name: 'FGVTN', available_at_lc: false, description: 'Aguardando confirmação' },
    { id: 'bigfer', name: 'Bigfer', available_at_lc: false, description: 'Aguardando confirmação' },
    { id: 'renna', name: 'Renna', available_at_lc: false, description: 'Aguardando confirmação' },
    { id: 'rometal', name: 'Rometal', available_at_lc: false, description: 'Aguardando confirmação' },
    { id: 'ducasse', name: 'Ducasse', available_at_lc: false, description: 'Aguardando confirmação' },
];

export const HARDWARE_CATEGORIES = [
    { id: 'handles', name: 'Puxadores', icon: 'fa-hand-holding' },
    { id: 'hinges', name: 'dobradiças', icon: 'fa-cube' },
    { id: 'slides', name: 'corrediças', icon: 'fa-arrows-alt-h' },
    { id: 'tracks', name: 'trilhos', icon: 'fa-cube' },
    { id: 'lighting', name: 'iluminação', icon: 'fa-lightbulb' },
    { id: 'movimento', name: 'sistema de movimento', icon: 'fa-cog' },
];

export const HANDLE_TYPES = [
    {
        id: 'alca',
        name: 'Alça',
        brand_id: 'generic',
        available_at_lc: true,
        description: 'Puxador clássico em alça cilíndrica.',
        material: 'aluminio',
        finish: 'preto',
        modelPath: null,
    },
    {
        id: 'botao',
        name: 'Botão',
        brand_id: 'generic',
        available_at_lc: true,
        description: 'Puxador embutido tipo botão redondo.',
        material: 'aluminio',
        finish: 'preto',
        modelPath: null,
    },
    {
        id: 'cava',
        name: 'Cava (recesso)',
        brand_id: 'generic',
        available_at_lc: true,
        description: 'Puxador em cava reto com recesso na porta.',
        material: 'aluminio',
        finish: 'preto',
        modelPath: null,
    },
    {
        id: 'perfil',
        name: 'Perfil L',
        brand_id: 'generic',
        available_at_lc: true,
        description: 'Puxador em perfil L embutido na borda.',
        material: 'aluminio',
        finish: 'preto',
        modelPath: null,
    },
    {
        id: 'concha',
        name: 'Concha',
        brand_id: 'generic',
        available_at_lc: true,
        description: 'Puxador em formato de concha ergonômica.',
        material: 'zamac',
        finish: 'preto',
        modelPath: null,
    },
    {
        id: 'embutido',
        name: 'Embutido',
        brand_id: 'generic',
        available_at_lc: true,
        description: 'Puxador totalmente embutido na porta.',
        material: 'aluminio',
        finish: 'preto',
        modelPath: null,
    },
    {
        id: 'touch',
        name: 'Touch (toque)',
        brand_id: 'generic',
        available_at_lc: true,
        description: 'Abertura por toque, sem puxador visível.',
        material: null,
        finish: null,
        modelPath: null,
    },
    {
        id: 'nenhum',
        name: 'Nenhum',
        brand_id: 'generic',
        available_at_lc: true,
        description: 'Sem puxador (portas com fechamento automático).',
        material: null,
        finish: null,
        modelPath: null,
    },
];

export const HANDLE_MATERIALS = [
    { id: 'aluminio', name: 'Alumínio', available_at_lc: true },
    { id: 'inox', name: 'Inox', available_at_lc: false, description: 'Aguardando confirmação' },
    { id: 'zamac', name: 'Zamac', available_at_lc: false, description: 'Aguardando confirmação' },
];

export const HANDLE_FINISHES = [
    { id: 'preto', name: 'Preto', hex: '#1A1714', available_at_lc: true },
    { id: 'dourado', name: 'Dourado', hex: '#B8935A', available_at_lc: false, description: 'Aguardando confirmação' },
    { id: 'champanhe', name: 'Champanhe', hex: '#D4C8B0', available_at_lc: false, description: 'Aguardando confirmação' },
    { id: 'cromado', name: 'Cromado', hex: '#C0C0C0', available_at_lc: false, description: 'Aguardando confirmação' },
    { id: 'titanio', name: 'Titânio', hex: '#7A7A7A', available_at_lc: false, description: 'Aguardando confirmação' },
    { id: 'aco_escovado', name: 'Aço Escovado', hex: '#8A8A8A', available_at_lc: false, description: 'Aguardando confirmação' },
];

export const LED_OPTIONS = [
    { id: 'none', name: 'Sem LED', available_at_lc: true, modelPath: null },
    { id: 'fita_interno', name: 'Fita LED interna', available_at_lc: true, modelPath: null, description: 'Iluminação interna do guarda-roupa' },
    { id: 'perfil_inferior', name: 'Perfil inferior', available_at_lc: false, modelPath: null, description: 'Aguardando confirmação' },
    { id: 'nicho', name: 'Iluminação de nicho', available_at_lc: false, modelPath: null, description: 'Aguardando confirmação' },
    { id: 'sensor', name: 'Com sensor de presença', available_at_lc: false, modelPath: null, description: 'Aguardando confirmação' },
];

export const COMPATIBILITY_RULES = [
    {
        component_type: 'door',
        component_id: 'all',
        hardware_type: 'hinges',
        is_compatible: true,
        conditions: 'door_hinge_side = left|right',
    },
    {
        component_type: 'drawer',
        component_id: 'all',
        hardware_type: 'slides',
        is_compatible: true,
        conditions: 'drawer_width >= 20cm and drawer_depth >= 40cm',
    },
    {
        component_type: 'door',
        component_id: 'all',
        hardware_type: 'tracks',
        is_compatible: false,
        conditions: 'use sliding_door_system instead',
    },
    {
        component_type: 'drawer',
        component_id: 'all',
        hardware_type: 'handles',
        is_compatible: true,
        conditions: 'handle_type in [alca, botao, cava, perfil, concha]',
    },
];

export function getHandleById(id) {
    return HANDLE_TYPES.find(h => h.id === id) || null;
}

export function getAvailableHandles() {
    return HANDLE_TYPES.filter(h => h.available_at_lc);
}

export function checkCompatibility(componentType, componentId, hardwareType) {
    const rule = COMPATIBILITY_RULES.find(
        r => (r.component_id === componentId || r.component_id === 'all') &&
             r.component_type === componentType &&
             r.hardware_type === hardwareType
    );
    return rule ? rule.is_compatible : true;
}
