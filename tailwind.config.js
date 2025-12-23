/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './*.php',
    './inc/**/*.php',
    './template-parts/**/*.php',
    './pages/**/*.php',
    './assets/js/**/*.js',
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          DEFAULT: '#059669',
          dark: '#047857',
          darker: '#065f46',
          light: '#10b981',
        },
        secondary: {
          DEFAULT: '#000000',
          light: '#1a1a1a',
          dark: '#000000',
        },
        accent: {
          DEFAULT: '#ffeb3b',
          light: '#fff9c4',
          dark: '#fbc02d',
          gold: '#d4af37', // Added for new design
          red: '#ef4444',  // Added for new design
        },
        ink: {
          primary: '#2c3e50', // Added for new design
        },
        paper: {
          warm: '#fdfbf7', // Added for new design
        },
        highlight: {
          blue: '#3b82f6', // Added for new design
        },
        // Book Theme - 補助金図鑑デザインシステム
        'book': {
          'cover': '#2c3e50',        // 濃紺（本の表紙）
          'cover-dark': '#1a252f',   // 濃紺ダーク
          'gold': '#d4af37',         // 金箔
          'gold-light': '#e6c85c',   // 金箔ライト
          'paper': '#fdfbf7',        // 和紙・クリーム
          'paper-dark': '#f5f0e6',   // 和紙ダーク
          'ink': '#2c3e50',          // インク色
          'spine': '#1e3a5f',        // 背表紙
        },
        'desk-wood': '#2c241b',
        'book-cover': '#1a2634',
        'paper-shadow': '#e3dac9',
        'ink-secondary': '#595959',
        // 難易度カラー
        'difficulty': {
          'low': '#10b981',          // 緑（易しい）
          'medium': '#f59e0b',       // オレンジ（普通）
          'high': '#ef4444',         // 赤（難しい）
        },
        // ステータスカラー
        'status': {
          'open': '#10b981',         // 募集中
          'closing': '#f59e0b',      // 締切間近
          'closed': '#6b7280',       // 募集終了
          'upcoming': '#3b82f6',     // 募集予定
        }
      },
      fontFamily: {
        'inter': ['Inter', 'sans-serif'],
        'outfit': ['Outfit', 'sans-serif'],
        'space': ['Space Grotesk', 'sans-serif'],
        'noto': ['Noto Sans JP', 'sans-serif'],
        // Book Theme フォント
        'serif': ['Noto Serif JP', 'Georgia', 'serif'],
        'sans': ['Noto Sans JP', 'Helvetica Neue', 'sans-serif'],
      },
      fontSize: {
        'h1': 'clamp(28px, 4vw, 36px)',
        'h2': 'clamp(22px, 3vw, 28px)',
        'h3': 'clamp(18px, 2.5vw, 22px)',
        'h4': '18px',
        'h5': '16px',
        'h6': '14px',
      },
      spacing: {
        '0': '0',
        '1': '4px',
        '2': '8px',
        '3': '12px',
        '4': '16px',
        '5': '20px',
        '6': '24px',
        '8': '32px',
        '10': '40px',
        '12': '48px',
        '16': '64px',
        '20': '80px',
        '24': '96px',
        '32': '128px',
      },
      borderRadius: {
        'sm': '4px',
        'DEFAULT': '8px',
        'md': '12px',
        'lg': '16px',
        'xl': '20px',
        '2xl': '24px',
        'full': '9999px',
      },
      boxShadow: {
        'sm': '0 1px 2px rgba(0, 0, 0, 0.05)',
        'DEFAULT': '0 2px 8px rgba(0, 0, 0, 0.08)',
        'md': '0 4px 12px rgba(0, 0, 0, 0.1)',
        'lg': '0 8px 24px rgba(0, 0, 0, 0.12)',
        'xl': '0 12px 32px rgba(0, 0, 0, 0.15)',
        'book-depth': '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05), 0 20px 25px -5px rgba(0, 0, 0, 0.1)', // Added for new design
      },
      transitionDuration: {
        'DEFAULT': '200ms',
        'fast': '150ms',
        'slow': '300ms',
      },
    },
  },
  plugins: [],
}
