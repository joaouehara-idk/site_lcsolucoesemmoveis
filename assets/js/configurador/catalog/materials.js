/**
 * Material Catalog Data
 *
 * STRUCTURE (ready for manufacturer data):
 *   brands → lines → patterns → textures → finishes → thicknesses → assets
 *
 * Current state: placeholder color palette. Real manufacturer data
 * (Greenplac, Duratex, Arauco, etc.) will populate this structure when
 * the data is confirmed by the LC team.
 *
 * Rule: "available_at_lc" must be true ONLY for materials the LC actually
 * stocks. Items with available_at_lc=false are "Referência de catálogo".
 */
export const MATERIAL_BRANDS = [
    {
        id: 'generic',
        name: 'Cor padrão LC',
        available_at_lc: true,
        description: 'Cores padrão oferecidas pela LC Soluções em Móveis',
    },
    {
        id: 'greenplac',
        name: 'Greenplac',
        available_at_lc: false,
        description: 'Fabricante de MDF — aguardando confirmação de disponibilidade na LC',
    },
    {
        id: 'duratex',
        name: 'Duratex',
        available_at_lc: false,
        description: 'Fabricante de MDF — aguardando confirmação de disponibilidade na LC',
    },
    {
        id: 'arauco',
        name: 'Arauco',
        available_at_lc: false,
        description: 'Fabricante de MDF — aguardando confirmação de disponibilidade na LC',
    },
    {
        id: 'guararapes',
        name: 'Guararapes',
        available_at_lc: false,
        description: 'Fabricante de MDF — aguardando confirmação de disponibilidade na LC',
    },
    {
        id: 'berneck',
        name: 'Berneck',
        available_at_lc: false,
        description: 'Fabricante de MDF — aguardando confirmação de disponibilidade na LC',
    },
    {
        id: 'eucatex',
        name: 'Eucatex',
        available_at_lc: false,
        description: 'Fabricante de MDF — aguardando confirmação de disponibilidade na LC',
    },
    {
        id: 'placas_do_brasil',
        name: 'Placas do Brasil',
        available_at_lc: false,
        description: 'Fabricante de MDF — aguardando confirmação de disponibilidade na LC',
    },
    {
        id: 'sudati',
        name: 'Sudati',
        available_at_lc: false,
        description: 'Fabricante de MDF — aguardando confirmação de disponibilidade na LC',
    },
    {
        id: 'floraplac',
        name: 'Floraplac / Flora',
        available_at_lc: false,
        description: 'Fabricante de MDF — aguardando confirmação de disponibilidade na LC',
    },
    {
        id: 'fibraplac',
        name: 'Fibraplac',
        available_at_lc: false,
        description: 'Fabricante de MDF — aguardando confirmação de disponibilidade na LC',
    },
];

// Material lines (mapped to brands)
export const MATERIAL_LINES = [
    // Generic LC standard colors
    { id: 'lc_padrao', brand_id: 'generic', name: 'Cores padrão LC', available_at_lc: true },
    // Greenplac lines (placeholder until confirmed)
    { id: 'greenplac_colore', brand_id: 'greenplac', name: 'Colore', available_at_lc: false },
    { id: 'greenplac_texture', brand_id: 'greenplac', name: 'Texture', available_at_lc: false },
    { id: 'greenplac_essenziale', brand_id: 'greenplac', name: 'Essenziale', available_at_lc: false },
    { id: 'greenplac_matiz', brand_id: 'greenplac', name: 'Matiz', available_at_lc: false },
    { id: 'greenplac_natural', brand_id: 'greenplac', name: 'Natural', available_at_lc: false },
    { id: 'greenplac_decore', brand_id: 'greenplac', name: 'Decore', available_at_lc: false },
    { id: 'greenplac_classicos', brand_id: 'greenplac', name: 'Clássicos', available_at_lc: false },
    { id: 'greenplac_mdf_green', brand_id: 'greenplac', name: 'MDF Green', available_at_lc: false },
    // Duratex lines (placeholder until confirmed)
    { id: 'duratex_essencial', brand_id: 'duratex', name: 'Essencial', available_at_lc: false },
    { id: 'duratex_design', brand_id: 'duratex', name: 'Design', available_at_lc: false },
    { id: 'duratex_cristallo', brand_id: 'duratex', name: 'Cristallo', available_at_lc: false },
    { id: 'duratex_velluto', brand_id: 'duratex', name: 'Velluto', available_at_lc: false },
    { id: 'duratex_trama', brand_id: 'duratex', name: 'Trama', available_at_lc: false },
    { id: 'duratex_conceito', brand_id: 'duratex', name: 'Conceito', available_at_lc: false },
    { id: 'duratex_acetinatta', brand_id: 'duratex', name: 'Acetinatta', available_at_lc: false },
    { id: 'duratex_you', brand_id: 'duratex', name: 'Duratex You', available_at_lc: false },
    // Arauco lines (placeholder until confirmed)
    { id: 'arauco_madeiras', brand_id: 'arauco', name: 'Madeiras', available_at_lc: false },
    { id: 'arauco_madeiras_brasileiras', brand_id: 'arauco', name: 'Madeiras Brasileiras', available_at_lc: false },
    { id: 'arauco_cores', brand_id: 'arauco', name: 'Cores', available_at_lc: false },
    { id: 'arauco_metais', brand_id: 'arauco', name: 'Metais', available_at_lc: false },
    { id: 'arauco_tecidos', brand_id: 'arauco', name: 'Tecidos', available_at_lc: false },
    { id: 'arauco_pedras', brand_id: 'arauco', name: 'Pedras', available_at_lc: false },
    // Arauco texture names (placeholder until confirmed)
    { id: 'arauco_bold', brand_id: 'arauco', name: 'Bold', available_at_lc: false },
    { id: 'arauco_chess', brand_id: 'arauco', name: 'Chess', available_at_lc: false },
    { id: 'arauco_couro', brand_id: 'arauco', name: 'Couro', available_at_lc: false },
    { id: 'arauco_dueto', brand_id: 'arauco', name: 'Dueto', available_at_lc: false },
    { id: 'arauco_fosco', brand_id: 'arauco', name: 'Fosco', available_at_lc: false },
    { id: 'arauco_liso', brand_id: 'arauco', name: 'Liso', available_at_lc: false },
    { id: 'arauco_matt', brand_id: 'arauco', name: 'Matt', available_at_lc: false },
    { id: 'arauco_nature', brand_id: 'arauco', name: 'Nature', available_at_lc: false },
    { id: 'arauco_poro', brand_id: 'arauco', name: 'Poro', available_at_lc: false },
    { id: 'arauco_sethos', brand_id: 'arauco', name: 'Sethos', available_at_lc: false },
    { id: 'arauco_trend', brand_id: 'arauco', name: 'Trend', available_at_lc: false },
    { id: 'arauco_tx', brand_id: 'arauco', name: 'TX', available_at_lc: false },
    { id: 'arauco_vert', brand_id: 'arauco', name: 'Vert', available_at_lc: false },
    // Guararapes, Berneck, Eucatex, etc. lines (placeholders)
    { id: 'guararapes_cores', brand_id: 'guararapes', name: 'Cores', available_at_lc: false },
    { id: 'guararapes_madeiras', brand_id: 'guararapes', name: 'Madeiras', available_at_lc: false },
    { id: 'berneck_mdf', brand_id: 'berneck', name: 'MDF', available_at_lc: false },
    { id: 'berneck_mdf_plus', brand_id: 'berneck', name: 'MDF PLUS', available_at_lc: false },
    { id: 'berneck_mdf_bp', brand_id: 'berneck', name: 'MDF BP', available_at_lc: false },
    { id: 'berneck_mdp', brand_id: 'berneck', name: 'MDP', available_at_lc: false },
    { id: 'berneck_hdf', brand_id: 'berneck', name: 'HDF', available_at_lc: false },
    { id: 'eucatex_bp', brand_id: 'eucatex', name: 'BP', available_at_lc: false },
    { id: 'eucatex_lacca', brand_id: 'eucatex', name: 'Lacca', available_at_lc: false },
    { id: 'eucatex_matt', brand_id: 'eucatex', name: 'Matt', available_at_lc: false },
    { id: 'eucatex_grafis', brand_id: 'eucatex', name: 'Grafis', available_at_lc: false },
    { id: 'eucatex_raizes', brand_id: 'eucatex', name: 'Raízes', available_at_lc: false },
    { id: 'eucatex_poro_supermatt', brand_id: 'eucatex', name: 'Poro Supermatt', available_at_lc: false },
];

export const FINISH_TYPES = [
    { id: 'melamina', name: 'Melamina', roughness: 0.75, metalness: 0.0, hasGrain: false },
    { id: 'texturizado', name: 'Texturizado (madeira)', roughness: 0.85, metalness: 0.0, hasGrain: true },
    { id: 'liso', name: 'Liso (fosco)', roughness: 0.55, metalness: 0.0, hasGrain: false },
    { id: 'laca', name: 'Laca (brilhante)', roughness: 0.15, metalness: 0.12, hasGrain: false },
    { id: 'madeira', name: 'Madeira natural', roughness: 0.8, metalness: 0.0, hasGrain: true },
    { id: 'acetinado', name: 'Acetinado', roughness: 0.45, metalness: 0.02, hasGrain: false },
];

export const COLOR_PRESETS = [
    { id: 'branco', name: 'Branco', hex: '#ECEAE3', finish: 'melamina', available_at_lc: true },
    { id: 'offwhite', name: 'Off-white', hex: '#E8E0D3', finish: 'melamina', available_at_lc: true },
    { id: 'bege', name: 'Bege', hex: '#D8C3A5', finish: 'melamina', available_at_lc: true },
    { id: 'caramelo', name: 'Caramelo', hex: '#C8A87C', finish: 'texturizado', available_at_lc: true },
    { id: 'madeira_clara', name: 'Madeira Clara', hex: '#C9A876', finish: 'texturizado', available_at_lc: true },
    { id: 'carvalho', name: 'Carvalho', hex: '#C9A876', finish: 'texturizado', available_at_lc: true },
    { id: 'nogueira', name: 'Nogueira', hex: '#6B5340', finish: 'texturizado', available_at_lc: true },
    { id: 'wenge', name: 'Wengué', hex: '#3A2E26', finish: 'texturizado', available_at_lc: true },
    { id: 'cinza', name: 'Cinza', hex: '#9A9A9A', finish: 'liso', available_at_lc: true },
    { id: 'grafite', name: 'Grafite', hex: '#4A4A4A', finish: 'liso', available_at_lc: true },
    { id: 'preto', name: 'Preto', hex: '#2B2B2B', finish: 'laco', available_at_lc: true },
];

export const MATERIAL_THICKNESSES = [
    { id: 6, label: '6 mm', available_at_lc: false },
    { id: 9, label: '9 mm', available_at_lc: false },
    { id: 12, label: '12 mm', available_at_lc: true },
    { id: 15, label: '15 mm', available_at_lc: true },
    { id: 18, label: '18 mm', available_at_lc: true },
    { id: 25, label: '25 mm', available_at_lc: false },
];

export const COLOR_APPROX_WARNING =
    'As cores exibidas na tela são uma representação digital e podem apresentar diferenças em relação à amostra física. ' +
    'Para especificação final, considere a amostra física do fabricante.';
