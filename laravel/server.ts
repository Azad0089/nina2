import express, { Request, Response, NextFunction } from 'express';
import session from 'express-session';
import cookieParser from 'cookie-parser';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const app = express();
const PORT = process.env.PORT ? parseInt(process.env.PORT, 10) : 3000;

app.set('trust proxy', 1);

// Middleware
app.use(express.urlencoded({ extended: true }));
app.use(express.json());
app.use((cookieParser as any)());
app.use((session as any)({
  secret: process.env.SESSION_SECRET || 'nina-sucks-microblogging-secret-2026',
  resave: false,
  saveUninitialized: true,
  cookie: { maxAge: 30 * 24 * 60 * 60 * 1000 }
}));

// Setup view engine
app.set('view engine', 'ejs');
app.set('views', path.join(__dirname, 'views'));

// Static files
app.use('/css', express.static(path.join(__dirname, 'css')));
app.use('/js', express.static(path.join(__dirname, 'js')));
app.use('/assets', express.static(path.join(__dirname, 'assets')));

// In-memory data store
interface User {
  id: number;
  username: string;
  email: string;
  role: 'user' | 'admin';
  status: 'active' | 'suspended' | 'banned';
  points: number;
  badge: string;
  blog: string;
}

interface Comment {
  id: number;
  postId: number;
  userId: number;
  username: string;
  badge: string;
  comment: string;
  createdAt: number;
}

interface Advertisement {
  id: number;
  title: string;
  sponsor_name: string;
  link_url: string;
  image_url: string;
  description: string;
  placement: string;
  is_active: boolean;
}

interface HeroSlide {
  color: string;
  img: string;
  alt: string;
  x: number; y: number; z: number; mx: number; my: number; mz: number;
  title: string;
  text: string;
  cta: Array<{ label: string; href: string; class: string; modal?: string }>;
}

interface Blog {
  id: number;
  userId: number;
  slug: string;
  title: string;
  tagline: string;
  color: string;
  avatar_path: string;
  cover_path: string;
  is_featured: number;
  bio: string;
  website1?: string;
  website2?: string;
  website3?: string;
  contact_email?: string;
  badge: string;
}

interface Post {
  id: number;
  blog: string;
  blog_id: number;
  color: string;
  badge: string;
  avatar: string;
  type: 'photo' | 'video' | 'text' | 'quote' | 'link' | 'chat';
  heading?: string;
  body?: string;
  caption?: string;
  alt?: string;
  img?: string;
  duration?: string;
  quote?: string;
  source?: string;
  title?: string;
  domain?: string;
  url?: string;
  lines?: [string, string][];
  crop?: { x: number; y: number; z: number; ratio?: string };
  tags: string[];
  like_count: number;
  reblog_count: number;
  minutes: number;
  createdAt: number;
  comments?: Comment[];
}

const users: Map<string, User> = new Map();
const blogs: Map<string, Blog> = new Map();
let posts: Post[] = [];
const likes: Set<string> = new Set(); // userId:postId
const follows: Set<string> = new Set(); // userId:blogSlug
const reports: Array<{ target_type: string; target_id: string; reason: string; timestamp: number }> = [];
const supportTickets: Array<{ name: string; email: string; topic: string; message: string; timestamp: number }> = [];
const postComments: Map<number, Comment[]> = new Map();

let advertisements: Advertisement[] = [
  {
    id: 1,
    title: 'Neon Nights Audio: Hi-Fi Cyberpunk Soundscapes',
    sponsor_name: 'Synthwave Radio FM',
    link_url: 'https://retro-synth.fm',
    image_url: 'hero.jpg',
    description: 'Listen to 24/7 dark electro and lo-fi beats curated specifically for writers and nocturnal creators.',
    placement: 'sidebar',
    is_active: true
  },
  {
    id: 2,
    title: 'Creator Hardware: Pro Lighting & Studio Mics',
    sponsor_name: 'StudioPro Gear',
    link_url: '/advertising.php',
    image_url: 'hero.jpg',
    description: 'Upgrade your aesthetic with neon ambient LED bars and crisp recording gear.',
    placement: 'sidebar',
    is_active: true
  }
];

let heroSlidesList: HeroSlide[] = [
  {
    color: '#ff2bd6',
    img: 'hero.jpg',
    alt: 'Neon-lit desk with laptop and city lights',
    x: 10, y: 22, z: 1.5, mx: 44, my: 30, mz: 1.25,
    title: 'Post it loud.',
    text: 'Text, photos, quotes, links, chats and video on a blog that looks exactly how you want it.',
    cta: [
      { label: 'Create your blog', href: 'register.php', class: 'btn-fill', modal: 'register' },
      { label: 'Explore posts', href: '#feed', class: 'btn-ghost' }
    ]
  },
  {
    color: '#19e3ff',
    img: 'hero.jpg',
    alt: 'Neon sign glowing on a window',
    x: 62, y: 0, z: 2.3, mx: 72, my: 0, mz: 1.5,
    title: 'One login. Unlimited blogs.',
    text: 'Every blog gets its own colors, cover and subdomain. Your first blog doubles as your profile, with bio, links and contact.',
    cta: [
      { label: 'Start a blog', href: 'register.php', class: 'btn-fill', modal: 'register' },
      { label: 'See how it works', href: 'support.php', class: 'btn-ghost' }
    ]
  },
  {
    color: '#ffd60a',
    img: 'hero.jpg',
    alt: 'Sleeping cat, candle and cushion',
    x: 93, y: 91, z: 2.2, mx: 94, my: 90, mz: 1.5,
    title: 'Boost what you make.',
    text: 'Premium members earn monthly points to feature posts and profiles on Explore. Buy views or flat-rate placements in the ad marketplace.',
    cta: [
      { label: 'Open the marketplace', href: 'advertising.php', class: 'btn-fill' },
      { label: 'Go premium', href: 'premium.php', class: 'btn-ghost' }
    ]
  }
];

let nextUserId = 1;
let nextBlogId = 1;
let nextPostId = 1;
let nextCommentId = 1;
let nextAdId = 3;

// Seed initial users
function seedUser(username: string, email: string, role: 'user' | 'admin', badge: string, points = 1000): User {
  const user: User = {
    id: nextUserId++,
    username,
    email,
    role,
    status: 'active',
    points,
    badge,
    blog: username
  };
  users.set(username.toLowerCase(), user);
  return user;
}

const adminUser = seedUser('admin', 'admin@example.com', 'admin', 'vip', 25000);
const vixenUser = seedUser('neon_vixen', 'vixen@nina.sucks', 'user', 'vip', 8500);
const ghostUser = seedUser('cyber_ghost', 'ghost@nina.sucks', 'user', 'pro', 4200);
const museUser = seedUser('velvet_muse', 'muse@nina.sucks', 'user', 'id', 6100);
const waveUser = seedUser('retro_wave', 'wave@nina.sucks', 'user', 'pro', 3800);

// Seed initial blogs
function seedBlog(user: User, title: string, tagline: string, color: string, bio: string, extra?: Partial<Blog>): Blog {
  const blog: Blog = {
    id: nextBlogId++,
    userId: user.id,
    slug: user.username,
    title,
    tagline,
    color,
    avatar_path: '',
    cover_path: 'hero.jpg',
    is_featured: 1,
    bio,
    badge: user.badge,
    ...extra
  };
  blogs.set(user.username.toLowerCase(), blog);
  return blog;
}

seedBlog(adminUser, 'Admin Central', 'Official staff updates and community notices', '#ff2bd6', 'Platform governance, updates & help.');
seedBlog(vixenUser, 'Neon Vixen', 'Midnight aesthetics, neon glow & cyber fantasies', '#ff2bd6', 'Creating vivid visual aesthetics in the heart of the neon underground. DM for collaborations.', { contact_email: 'neon@nina.sucks' });
seedBlog(ghostUser, 'Cyber Ghost', 'Dark synth, glitches and late night thoughts', '#19e3ff', 'Lost in the digital realm. Exploring dystopian visuals and soundscapes.', { website1: 'https://cyberghost.art' });
seedBlog(museUser, 'Velvet Muse', 'Sensual poetry, intimate photography & art', '#ffd60a', 'Visual artist and writer. Celebrating beauty, softness and unfiltered expression.', { website1: 'https://velvetmuse.gallery' });
seedBlog(waveUser, 'Retro Wave', '80s nostalgia, VHS tapes and analog dreams', '#8a3dff', 'Living in 1984 through a CRT monitor. Mixtapes, neon grids, and vintage synths.');

// Seed initial posts
posts = [
  {
    id: nextPostId++,
    blog: 'neon_vixen',
    blog_id: 2,
    color: '#ff2bd6',
    badge: 'vip',
    avatar: '',
    type: 'photo',
    img: 'hero.jpg',
    caption: 'Midnight reflections across the skyline. The neon glow never sleeps in this city.',
    alt: 'Neon cityscape with glowing magenta lights',
    crop: { x: 10, y: 22, z: 1.5, ratio: '16/10' },
    tags: ['neon', 'nightshift', 'cyberpunk'],
    like_count: 84,
    reblog_count: 23,
    minutes: 14,
    createdAt: Date.now() - 14 * 60000
  },
  {
    id: nextPostId++,
    blog: 'velvet_muse',
    blog_id: 4,
    color: '#ffd60a',
    badge: 'id',
    avatar: '',
    type: 'quote',
    quote: 'We are all creatures of the electric twilight, looking for something real between the glow of our screens.',
    source: 'Velvet Confessions, Vol. 1',
    tags: ['thoughts', 'poetry', 'nightshift'],
    like_count: 67,
    reblog_count: 15,
    minutes: 28,
    createdAt: Date.now() - 28 * 60000
  },
  {
    id: nextPostId++,
    blog: 'cyber_ghost',
    blog_id: 3,
    color: '#19e3ff',
    badge: 'pro',
    avatar: '',
    type: 'text',
    heading: 'The Art of Unfiltered Microblogging',
    body: 'Remember when blogging felt personal and free? No algorithms dictating what you must see, no corporate sanitized feeds, just raw thoughts, photos, and mutuals reblogging whatever sparks joy at 3 AM.\n\nNina.Sucks brings that feeling back. Unlimited blogs, customized colors, no barriers.',
    tags: ['nostalgia', 'blogging', 'freedom'],
    like_count: 142,
    reblog_count: 51,
    minutes: 52,
    createdAt: Date.now() - 52 * 60000
  },
  {
    id: nextPostId++,
    blog: 'retro_wave',
    blog_id: 5,
    color: '#8a3dff',
    badge: 'pro',
    avatar: '',
    type: 'link',
    title: 'Synthwave Radio - 24/7 Chill Beats for Night Owls',
    domain: 'retro-synth.fm',
    url: 'https://retro-synth.fm',
    caption: 'Tune in to the latest stream, perfect background soundtrack for writing posts.',
    tags: ['music', 'synthwave', 'bts'],
    like_count: 38,
    reblog_count: 9,
    minutes: 85,
    createdAt: Date.now() - 85 * 60000
  },
  {
    id: nextPostId++,
    blog: 'neon_vixen',
    blog_id: 2,
    color: '#ff2bd6',
    badge: 'vip',
    avatar: '',
    type: 'chat',
    lines: [
      ['Nova', 'Are you still awake?'],
      ['Vixen', 'Always. Editing new shoots under the purple glow.'],
      ['Nova', 'Send a preview before it goes live!'],
      ['Vixen', 'Check the next post ;)']
    ],
    tags: ['chat', 'neon', 'bts'],
    like_count: 95,
    reblog_count: 18,
    minutes: 120,
    createdAt: Date.now() - 120 * 60000
  },
  {
    id: nextPostId++,
    blog: 'cyber_ghost',
    blog_id: 3,
    color: '#19e3ff',
    badge: 'pro',
    avatar: '',
    type: 'video',
    img: 'hero.jpg',
    duration: '0:45',
    caption: 'Short film teaser: Electric Dreams in Sector 7. Headphones recommended.',
    alt: 'Electric Dreams video preview',
    tags: ['video', 'cyberpunk', 'visuals'],
    like_count: 112,
    reblog_count: 40,
    minutes: 180,
    createdAt: Date.now() - 180 * 60000
  },
  {
    id: nextPostId++,
    blog: 'velvet_muse',
    blog_id: 4,
    color: '#ffd60a',
    badge: 'id',
    avatar: '',
    type: 'photo',
    img: 'hero.jpg',
    caption: 'Candlelight and velvet cushions. A quiet evening away from the storm.',
    alt: 'Cozy evening setting',
    crop: { x: 96, y: 92, z: 1.9, ratio: '1/1' },
    tags: ['cozy', 'selfie', 'lifestyle'],
    like_count: 73,
    reblog_count: 12,
    minutes: 240,
    createdAt: Date.now() - 240 * 60000
  },
  {
    id: nextPostId++,
    blog: 'retro_wave',
    blog_id: 5,
    color: '#8a3dff',
    badge: 'pro',
    avatar: '',
    type: 'quote',
    quote: 'The future was supposed to be chrome and neon; instead we got subscriptions and notifications. Take me back.',
    source: 'Analog Wanderer',
    tags: ['retro', 'nightshift'],
    like_count: 189,
    reblog_count: 64,
    minutes: 360,
    createdAt: Date.now() - 360 * 60000
  },
  {
    id: nextPostId++,
    blog: 'neon_vixen',
    blog_id: 2,
    color: '#ff2bd6',
    badge: 'vip',
    avatar: '',
    type: 'photo',
    img: 'hero.jpg',
    caption: 'Golden hour turning into magenta twilight. Rooftop photo sessions.',
    alt: 'Rooftop magenta sky',
    crop: { x: 62, y: 0, z: 2.3, ratio: '16/10' },
    tags: ['neon', 'lifestyle', 'bts'],
    like_count: 156,
    reblog_count: 39,
    minutes: 420,
    createdAt: Date.now() - 420 * 60000
  },
  {
    id: nextPostId++,
    blog: 'velvet_muse',
    blog_id: 4,
    color: '#ffd60a',
    badge: 'id',
    avatar: '',
    type: 'text',
    heading: 'Why Freedom of Expression Matters',
    body: 'Creative spaces thrive when artists are not censored or sanitized for corporate sponsors. Here, every post is yours, every theme is customizable, and the community supports real creators.',
    tags: ['community', 'art', 'creator'],
    like_count: 204,
    reblog_count: 88,
    minutes: 540,
    createdAt: Date.now() - 540 * 60000
  },
  {
    id: nextPostId++,
    blog: 'cyber_ghost',
    blog_id: 3,
    color: '#19e3ff',
    badge: 'pro',
    avatar: '',
    type: 'link',
    title: 'Underground Cyberpunk Aesthetic Archive',
    domain: 'cyberarchive.org',
    url: 'https://cyberarchive.org',
    caption: 'Huge collection of 90s cyber-graphics and retro renders.',
    tags: ['archive', 'cyberpunk'],
    like_count: 89,
    reblog_count: 27,
    minutes: 600,
    createdAt: Date.now() - 600 * 60000
  },
  {
    id: nextPostId++,
    blog: 'retro_wave',
    blog_id: 5,
    color: '#8a3dff',
    badge: 'pro',
    avatar: '',
    type: 'chat',
    lines: [
      ['Kira', 'Found that cassette deck we were talking about.'],
      ['Ray', 'Does it still play?'],
      ['Kira', 'Cleaned the heads, sounds like pure 1986.']
    ],
    tags: ['chat', 'retro'],
    like_count: 61,
    reblog_count: 14,
    minutes: 720,
    createdAt: Date.now() - 720 * 60000
  }
];

// Helper functions for template rendering
const siteHelpers = {
  url(p = '') {
    return '/' + p.replace(/^\//, '');
  },
  asset(f = '') {
    return '/assets/' + f.replace(/^\//, '');
  },
  media_url(f = '') {
    if (!f || f === 'hero.jpg') return '/assets/hero.jpg';
    if (f.startsWith('http')) return f;
    return '/assets/' + f.replace(/^\//, '');
  },
  icon(id: string, cls = '') {
    return `<svg class="ic${cls ? ' ' + cls : ''}" aria-hidden="true"><use href="#i-${id}"/></svg>`;
  },
  badge(type?: string) {
    if (type === 'pro') return '<span class="badge b-pro bd bd-pro" title="Premium verified"><svg class="ic"><use href="#i-verified"/></svg> PRO</span>';
    if (type === 'id') return '<span class="badge b-id bd bd-id" title="Identity verified"><svg class="ic"><use href="#i-verified"/></svg> ID</span>';
    if (type === 'vip') return '<span class="badge b-vip bd bd-vip" title="VIP member"><svg class="ic"><use href="#i-star"/></svg> VIP</span>';
    return '';
  },
  crop_style(c: { x?: number; y?: number; z?: number; ratio?: string } = {}) {
    const x = c.x ?? 50;
    const y = c.y ?? 50;
    const z = c.z ?? 1;
    let s = `--px:${x}%;--py:${y}%;--z:${z};`;
    if (c.ratio) s += `aspect-ratio:${c.ratio};`;
    return s;
  },
  fmt_count(n: number) {
    return n >= 1000 ? (n / 1000).toFixed(1).replace(/\.0$/, '') + 'k' : String(n);
  },
  time_ago(minutes: number) {
    if (minutes < 60) return `${minutes}m`;
    if (minutes < 1440) return `${Math.floor(minutes / 60)}h`;
    if (minutes < 10080) return `${Math.floor(minutes / 1440)}d`;
    return `${Math.floor(minutes / 10080)}w`;
  },
  initial(name: string) {
    return (name || 'N').charAt(0).toUpperCase();
  },
  site_name() {
    return 'Nina.Sucks';
  },
  site_logo_url() {
    return '/assets/logo.png';
  },
  site_favicon_url(sz = '32') {
    if (sz === '192') return '/assets/favicon-192.png';
    if (sz === 'apple') return '/assets/apple-touch-icon.png';
    return '/assets/favicon-32.png';
  },
  site_color_vars() {
    return '<style>:root{--pink:#ff2bd6;--cyan:#19e3ff;}</style>';
  },
  nav_items() {
    return [
      { key: 'explore', label: 'Explore', href: '', icon: 'compass' },
      { key: 'community', label: 'Community', href: 'community', icon: 'users' },
      { key: 'trending', label: 'Trending', href: 'trending.php', icon: 'flame' },
      { key: 'advertising', label: 'Advertising', href: 'advertising.php', icon: 'megaphone' },
      { key: 'premium', label: 'Premium', href: 'premium.php', icon: 'crown' },
      { key: 'support', label: 'Support', href: 'support.php', icon: 'help' }
    ];
  },
  footer_links() {
    return [
      { label: 'Terms', href: 'terms.php' },
      { label: 'Privacy', href: 'privacy.php' },
      { label: 'Rules', href: 'rules.php' },
      { label: '2257 & DMCA', href: 'dmca.php' },
      { label: 'Cookies', href: 'cookies.php' },
      { label: 'Contact', href: 'contact.php' }
    ];
  },
  hero_slides(user?: any) {
    if (user) {
      return heroSlidesList.map((s, idx) => {
        if (idx === 0) {
          return {
            ...s,
            cta: [
              { label: 'Create new post', href: 'dashboard/post.php', class: 'btn-fill' },
              { label: 'Explore feed', href: '#feed', class: 'btn-ghost' }
            ]
          };
        }
        return s;
      });
    }
    return heroSlidesList;
  },
  active_ads() {
    return advertisements.filter(a => a.is_active);
  },
  badge_legend() {
    return [
      { badge: 'pro', text: 'Premium: yearly supporter' },
      { badge: 'id', text: 'Verified: ID-checked creator' },
      { badge: 'vip', text: 'VIP: community favorite' }
    ];
  },
  post_types() {
    return {
      photo: ['Photo', 'photo'],
      text: ['Text', 'text'],
      quote: ['Quote', 'quote'],
      link: ['Link', 'link'],
      chat: ['Chat', 'chat'],
      video: ['Video', 'video']
    };
  }
};

// Global view variables middleware
app.use((req: Request, res: Response, next: NextFunction) => {
  const sessionUser = (req.session as any)?.user;
  res.locals = {
    ...res.locals,
    ...siteHelpers,
    currentUser: sessionUser || null,
    csrfToken: 'csrf-demo-token',
    flash: (req.session as any)?.flash || null,
    likedPosts: new Set(
      sessionUser
        ? [...likes].filter(k => k.startsWith(`${sessionUser.id}:`)).map(k => parseInt(k.split(':')[1], 10))
        : []
    ),
    followedBlogs: new Set(
      sessionUser
        ? [...follows].filter(k => k.startsWith(`${sessionUser.id}:`)).map(k => k.split(':')[1])
        : []
    )
  };
  delete (req.session as any)?.flash;
  next();
});

// Seed blog helper
function getFeaturedBlogs() {
  return [
    { name: 'neon_vixen', color: '#ff2bd6', badge: 'vip', tagline: 'Midnight aesthetics & neon glow', img: 'hero.jpg', href: 'blog.php?u=neon_vixen' },
    { name: 'cyber_ghost', color: '#19e3ff', badge: 'pro', tagline: 'Dark synth & late night thoughts', img: 'hero.jpg', href: 'blog.php?u=cyber_ghost' },
    { name: 'velvet_muse', color: '#ffd60a', badge: 'id', tagline: 'Sensual poetry & intimate art', img: 'hero.jpg', href: 'blog.php?u=velvet_muse' },
    { name: 'retro_wave', color: '#8a3dff', badge: 'pro', tagline: '80s nostalgia & analog dreams', img: 'hero.jpg', href: 'blog.php?u=retro_wave' }
  ];
}

function getTrendingTags(): [string, number][] {
  return [
    ['neon', 34],
    ['nightshift', 28],
    ['cyberpunk', 25],
    ['cozy', 19],
    ['bts', 18],
    ['retro', 16],
    ['selfie', 15],
    ['poetry', 12],
    ['art', 10],
    ['music', 9],
    ['visuals', 8],
    ['thoughts', 7]
  ];
}

function getSuggestedBlogs() {
  return [
    { id: 2, name: 'neon_vixen', color: '#ff2bd6', note: 'Featured' },
    { id: 3, name: 'cyber_ghost', color: '#19e3ff', note: 'Suggested' },
    { id: 4, name: 'velvet_muse', color: '#ffd60a', note: 'Verified' },
    { id: 5, name: 'retro_wave', color: '#8a3dff', note: 'Creator' }
  ];
}

// ==========================================
// ROUTES
// ==========================================

// 1. Home / Explore
app.get(['/', '/index.php'], (req: Request, res: Response) => {
  const typeFilter = String(req.query.type || 'all');
  const view = req.query.view === 'list' ? 'list' : 'grid';

  let filtered = posts.slice();
  if (typeFilter && typeFilter !== 'all') {
    filtered = filtered.filter(p => p.type === typeFilter);
  }

  const firstBatchCount = 8;
  const initialPosts = filtered.slice(0, firstBatchCount);

  res.render('index', {
    page_title: 'Nina.Sucks | Adult blogging community: text, photo, video, quote, link & chat',
    page_desc: 'Nina.Sucks is an 18+ blogging community. Start unlimited blogs, follow creators, reblog, boost your posts and explore the newest posts first, with no login wall.',
    page_canonical: 'http://localhost:3000/',
    active_nav: 'explore',
    type: typeFilter,
    view,
    posts: initialPosts,
    firstBatchCount,
    featuredBlogs: getFeaturedBlogs(),
    trendingTags: getTrendingTags(),
    suggestedBlogs: getSuggestedBlogs()
  });
});

// 2. Feed endpoint for infinite scroll (JS main.js calls /feed.php?offset=...&limit=...&type=...)
app.get(['/feed', '/feed.php'], (req: Request, res: Response) => {
  const offset = Math.max(0, parseInt(String(req.query.offset || '0'), 10));
  const limit = Math.min(24, Math.max(1, parseInt(String(req.query.limit || '6'), 10)));
  const typeFilter = String(req.query.type || 'all');

  let filtered = posts.slice();
  if (typeFilter && typeFilter !== 'all') {
    filtered = filtered.filter(p => p.type === typeFilter);
  }

  const batch = filtered.slice(offset, offset + limit);
  let html = '';
  for (const p of batch) {
    app.render('partials/card', { ...res.locals, p }, (err, rendered) => {
      if (!err && rendered) html += rendered;
    });
  }
  res.setHeader('Content-Type', 'text/html; charset=utf-8');
  res.send(html);
});

// 3. Trending
app.get(['/trending', '/trending.php'], (req: Request, res: Response) => {
  const tagQuery = req.query.tag ? String(req.query.tag).toLowerCase() : '';
  let topList = posts.slice().sort((a, b) => b.like_count - a.like_count);

  if (tagQuery) {
    topList = topList.filter(p => p.tags.some(t => t.toLowerCase() === tagQuery));
  }

  res.render('trending', {
    page_title: 'Trending | Nina.Sucks',
    page_desc: 'Trending tags, popular blogs and the most-liked posts on Nina.Sucks right now.',
    page_canonical: 'http://localhost:3000/trending.php',
    active_nav: 'trending',
    topPosts: topList.slice(0, 6),
    featuredBlogs: getFeaturedBlogs(),
    trendingTags: getTrendingTags()
  });
});

// 4. Individual Blog View
app.get(['/blog.php', '/blog/:slug'], (req: Request, res: Response) => {
  const slug = String(req.query.u || req.params.slug || 'neon_vixen').toLowerCase();
  const blog = blogs.get(slug) || {
    id: 99,
    userId: 99,
    slug,
    title: slug.charAt(0).toUpperCase() + slug.slice(1),
    tagline: 'Independent Creator',
    color: '#ff2bd6',
    avatar_path: '',
    cover_path: 'hero.jpg',
    is_featured: 1,
    bio: 'Welcome to my blog on Nina.Sucks.',
    badge: 'pro'
  };

  const typeFilter = String(req.query.type || 'all');
  let blogPosts = posts.filter(p => p.blog.toLowerCase() === slug);
  if (typeFilter && typeFilter !== 'all') {
    blogPosts = blogPosts.filter(p => p.type === typeFilter);
  }

  const currentUser = (req.session as any)?.user;
  const isOwner = currentUser && currentUser.username.toLowerCase() === slug;
  const isFollowing = currentUser && follows.has(`${currentUser.id}:${slug}`);

  res.render('blog', {
    page_title: `${blog.title} (@${blog.slug}) | Nina.Sucks`,
    page_desc: blog.tagline || `${blog.title} on Nina.Sucks`,
    page_canonical: `http://localhost:3000/blog.php?u=${encodeURIComponent(blog.slug)}`,
    active_nav: '',
    blog,
    posts: blogPosts,
    type: typeFilter,
    isOwner,
    isFollowing,
    followerCount: 42
  });
});

// 5. Individual Post View
app.get(['/post.php', '/post/:id'], (req: Request, res: Response) => {
  const id = parseInt(String(req.query.id || req.params.id || '1'), 10);
  const post = posts.find(p => p.id === id) || posts[0];
  post.comments = postComments.get(post.id) || [
    {
      id: 1,
      postId: post.id,
      userId: 2,
      username: 'neon_vixen',
      badge: 'vip',
      comment: 'Obsessed with the mood and lighting in this post! Truly stunning work.',
      createdAt: Date.now() - 45 * 60000
    },
    {
      id: 2,
      postId: post.id,
      userId: 3,
      username: 'cyber_ghost',
      badge: 'pro',
      comment: 'The analog texture on this is pure nostalgia. Keep posting more.',
      createdAt: Date.now() - 15 * 60000
    }
  ];

  res.render('post', {
    page_title: `${post.heading || post.caption || post.quote || 'Post #' + post.id} — @${post.blog}`,
    page_desc: post.body || post.caption || 'Nina.Sucks post',
    page_canonical: `http://localhost:3000/post.php?id=${post.id}`,
    active_nav: '',
    p: post
  });
});

// 6. Action APIs (like, follow, reblog, comment, report, contact)
app.post(['/actions/comment', '/actions/comment.php'], (req: Request, res: Response) => {
  const currentUser = (req.session as any)?.user;
  if (!currentUser) {
    if (req.xhr || req.headers.accept?.includes('json')) {
      return res.status(401).json({ ok: false, message: 'Please sign in to comment.' });
    }
    return res.redirect('/login.php');
  }

  const postId = parseInt(String(req.body.post_id || '1'), 10);
  const commentText = String(req.body.comment || '').trim();
  if (!commentText) {
    return res.status(400).json({ ok: false, message: 'Comment cannot be empty.' });
  }

  const newComment: Comment = {
    id: nextCommentId++,
    postId,
    userId: currentUser.id,
    username: currentUser.username,
    badge: currentUser.badge || 'pro',
    comment: commentText,
    createdAt: Date.now()
  };

  const list = postComments.get(postId) || [];
  list.unshift(newComment);
  postComments.set(postId, list);

  const post = posts.find(p => p.id === postId);
  if (post) post.comments = list;

  if (req.xhr || req.headers.accept?.includes('json')) {
    return res.json({
      ok: true,
      comment: newComment,
      count: list.length,
      message: 'Comment posted!'
    });
  }

  res.redirect(`/post.php?id=${postId}#comments`);
});

app.post(['/actions/like', '/actions/like.php'], (req: Request, res: Response) => {
  const currentUser = (req.session as any)?.user;
  if (!currentUser) {
    return res.status(401).json({ ok: false, message: 'Please sign in first.' });
  }

  const postId = parseInt(String(req.body.post_id || '0'), 10);
  const key = `${currentUser.id}:${postId}`;
  let liked = false;

  const post = posts.find(p => p.id === postId);
  if (likes.has(key)) {
    likes.delete(key);
    liked = false;
    if (post) post.like_count = Math.max(0, post.like_count - 1);
  } else {
    likes.add(key);
    liked = true;
    if (post) post.like_count += 1;
  }

  res.json({ ok: true, liked, count: post ? post.like_count : 0 });
});

app.post(['/actions/follow', '/actions/follow.php'], (req: Request, res: Response) => {
  const currentUser = (req.session as any)?.user;
  if (!currentUser) {
    return res.status(401).json({ ok: false, message: 'Please sign in first.' });
  }

  const rawBlog = String(req.body.blog || '').replace(/^@/, '').trim().toLowerCase();
  const key = `${currentUser.id}:${rawBlog}`;
  let following = false;

  if (follows.has(key)) {
    follows.delete(key);
    following = false;
  } else {
    follows.add(key);
    following = true;
  }

  res.json({ ok: true, following });
});

app.post(['/actions/reblog', '/actions/reblog.php'], (req: Request, res: Response) => {
  const currentUser = (req.session as any)?.user;
  if (!currentUser) {
    return res.status(401).json({ ok: false, message: 'Please sign in first.' });
  }

  const postId = parseInt(String(req.body.post_id || '0'), 10);
  const post = posts.find(p => p.id === postId);
  if (post) post.reblog_count += 1;

  res.json({ ok: true });
});

app.post(['/actions/report', '/actions/report.php'], (req: Request, res: Response) => {
  const target_type = String(req.body.target_type || 'post');
  const target_id = String(req.body.target_id || '');
  const reason = String(req.body.reason || '');

  reports.push({ target_type, target_id, reason, timestamp: Date.now() });
  res.json({ ok: true, message: 'Thank you for your report. Our moderation team will review it.' });
});

app.post(['/actions/contact', '/actions/contact.php'], (req: Request, res: Response) => {
  const { name, email, topic, message } = req.body;
  supportTickets.push({ name: String(name), email: String(email), topic: String(topic), message: String(message), timestamp: Date.now() });
  (req.session as any).flash = { success: 'Your message has been sent. We will get back to you shortly!' };
  res.redirect('/contact.php');
});

// 7. Auth Routes
app.post(['/auth/login', '/auth/login.php'], (req: Request, res: Response) => {
  const { login, password } = req.body;
  const username = String(login || '').trim().toLowerCase();

  // Support default admin login or any existing/auto-created user
  let user = users.get(username);
  if (!user) {
    // If not found, create a new user profile on the fly for convenience
    user = seedUser(username || 'member', `${username || 'member'}@example.com`, 'user', 'pro');
    seedBlog(user, username, 'Microblogging on Nina.Sucks', '#ff2bd6', 'Hello world! Welcome to my blog.');
  }

  (req.session as any).user = user;
  res.redirect('/');
});

app.get(['/login', '/login.php'], (_req: Request, res: Response) => {
  res.render('index', {
    page_title: 'Sign In | Nina.Sucks',
    page_desc: 'Sign in to Nina.Sucks',
    page_canonical: 'http://localhost:3000/login.php',
    active_nav: '',
    type: 'all',
    view: 'grid',
    posts: posts.slice(0, 8),
    firstBatchCount: 8,
    featuredBlogs: getFeaturedBlogs(),
    trendingTags: getTrendingTags(),
    suggestedBlogs: getSuggestedBlogs(),
    open_modal: 'login'
  });
});

app.post(['/auth/register', '/auth/register.php'], (req: Request, res: Response) => {
  const { username, email } = req.body;
  const cleanName = String(username || 'user' + Math.floor(Math.random() * 1000)).trim().toLowerCase();

  const user = seedUser(cleanName, String(email || `${cleanName}@example.com`), 'user', 'pro');
  seedBlog(user, cleanName, 'Microblogging on Nina.Sucks', '#ff2bd6', 'Welcome to my new blog!');

  (req.session as any).user = user;
  res.redirect('/dashboard');
});

app.get(['/register', '/register.php'], (_req: Request, res: Response) => {
  res.render('index', {
    page_title: 'Sign Up | Nina.Sucks',
    page_desc: 'Create your blog on Nina.Sucks',
    page_canonical: 'http://localhost:3000/register.php',
    active_nav: '',
    type: 'all',
    view: 'grid',
    posts: posts.slice(0, 8),
    firstBatchCount: 8,
    featuredBlogs: getFeaturedBlogs(),
    trendingTags: getTrendingTags(),
    suggestedBlogs: getSuggestedBlogs(),
    open_modal: 'register'
  });
});

app.get(['/logout', '/logout.php'], (req: Request, res: Response) => {
  req.session.destroy(() => {
    res.redirect('/');
  });
});

app.post(['/auth/forgot', '/auth/forgot.php'], (req: Request, res: Response) => {
  (req.session as any).flash = { forgot: 'Password reset link sent! Check your inbox.' };
  res.redirect('/forgot.php');
});

app.get(['/forgot', '/forgot.php'], (_req: Request, res: Response) => {
  res.render('index', {
    page_title: 'Reset Password | Nina.Sucks',
    page_desc: 'Reset your password',
    page_canonical: 'http://localhost:3000/forgot.php',
    active_nav: '',
    type: 'all',
    view: 'grid',
    posts: posts.slice(0, 8),
    firstBatchCount: 8,
    featuredBlogs: getFeaturedBlogs(),
    trendingTags: getTrendingTags(),
    suggestedBlogs: getSuggestedBlogs(),
    open_modal: 'forgot'
  });
});

// 8. Content / Marketing Pages
app.get(['/community', '/community.php'], (req: Request, res: Response) => {
  const featuredBlogs = Array.from(blogs.values()).slice(0, 3);
  res.render('community', {
    page_title: 'Creator Community | Nina.Sucks',
    page_desc: 'The uncensored hub for creator discovery, free expression, and collaborative microblogging.',
    page_canonical: 'http://localhost:3000/community',
    active_nav: 'community',
    settings: {
      community_title: 'Nina.Sucks Creator Community',
      community_tagline: 'The uncensored hub for creator discovery, free expression, and collaborative microblogging.',
      community_bio: "Welcome to the Nina.Sucks official community!\nConnect with like-minded creators, share stories, quotes, photos and chats without algorithmic barriers.\nCreate unlimited blogs, customize your pages, and earn points to boost your reach across the network.",
      community_url1: 'https://nina.sucks',
      community_url2: 'https://twitter.com/ninasucks',
      community_url3: 'https://t.me/ninasucks',
      community_email: 'community@nina.sucks',
    },
    featuredBlogs: featuredBlogs,
    communityPosts: posts.slice(0, 10),
  });
});

app.get(['/advertising', '/advertising.php'], (_req: Request, res: Response) => {
  res.render('advertising', {
    page_title: 'Advertising Marketplace | Nina.Sucks',
    page_desc: 'Promote your blog or posts on Nina.Sucks.',
    page_canonical: 'http://localhost:3000/advertising.php',
    active_nav: 'advertising'
  });
});

app.get(['/premium', '/premium.php'], (_req: Request, res: Response) => {
  res.render('premium', {
    page_title: 'Go Premium | Nina.Sucks',
    page_desc: 'Support Nina.Sucks and get a verified badge and monthly points.',
    page_canonical: 'http://localhost:3000/premium.php',
    active_nav: 'premium'
  });
});

app.get(['/support', '/support.php'], (_req: Request, res: Response) => {
  res.render('support', {
    page_title: 'Help Center | Nina.Sucks',
    page_desc: 'Answers about blogs, profiles, premium, points, and safety on Nina.Sucks.',
    page_canonical: 'http://localhost:3000/support.php',
    active_nav: 'support'
  });
});

// Static / legal content pages
const legalPages: Record<string, { title: string; lead: string; color: string; html: string; isContact?: boolean }> = {
  'terms.php': {
    title: 'Terms of Service',
    lead: 'Rules and conditions governing the use of Nina.Sucks services.',
    color: '#19e3ff',
    html: `
      <h2>1. Age Requirement</h2>
      <p>Nina.Sucks is strictly for users 18 years of age or older (or the age of legal majority in your jurisdiction). By accessing or creating an account, you affirm that you meet this requirement.</p>
      <h2>2. User Content &amp; Ownership</h2>
      <p>You retain full copyright and ownership of the original text, photos, quotes, and media you publish. You grant Nina.Sucks a worldwide license to host, display, and distribute your posts within the platform according to your privacy and blog settings.</p>
      <h2>3. Prohibited Conduct</h2>
      <p>Non-consensual content, harassment, copyright infringement, spam, and unlawful material are strictly prohibited and subject to immediate account termination.</p>
    `
  },
  'privacy.php': {
    title: 'Privacy Policy',
    lead: 'How we collect, use, and protect your information.',
    color: '#ffd60a',
    html: `
      <h2>Data We Collect</h2>
      <p>We collect your username, email address, IP address, and cookie tokens strictly necessary for authentication and session management.</p>
      <h2>No Third-Party Ad Trackers</h2>
      <p>We do not sell your personal data or tracking cookies to third-party ad networks.</p>
    `
  },
  'rules.php': {
    title: 'Community Rules',
    lead: 'Standards to keep Nina.Sucks creative, respectful, and safe.',
    color: '#ff2bd6',
    html: `
      <h2>Respect &amp; Consent</h2>
      <p>All adult content must feature fully consenting individuals who are at least 18 years old. Content violating consent will result in an immediate permanent ban and referral to authorities.</p>
      <h2>No Spam or Bot Raids</h2>
      <p>Automated spam, repetitive commercial marketing, and malicious links are not allowed.</p>
    `
  },
  'dmca.php': {
    title: '2257 Compliance &amp; DMCA',
    lead: 'Copyright notices and statutory recordkeeping compliance.',
    color: '#ff1f4b',
    html: `
      <h2>DMCA Notice &amp; Takedown</h2>
      <p>If you believe content hosted on Nina.Sucks infringes your copyright, submit a takedown notice via our Contact form including URL, description of work, and contact details.</p>
      <h2>18 U.S.C. 2257 Statement</h2>
      <p>All records required by 18 U.S.C. § 2257 and 28 C.F.R. Part 75 are maintained by the respective primary producers of the content.</p>
    `
  },
  'cookies.php': {
    title: 'Cookie Policy',
    lead: 'Information regarding session cookies and local storage.',
    color: '#8a3dff',
    html: `
      <h2>Essential Cookies</h2>
      <p>We use session cookies solely for login authentication, CSRF security, and storing your age gate verification preference.</p>
    `
  },
  'about.php': {
    title: 'About Nina.Sucks',
    lead: 'Decentralized expression, unlimited creative blogs, and pure adult microblogging.',
    color: '#ff2bd6',
    html: `
      <h2>Our Philosophy</h2>
      <p>Nina.Sucks was conceived as a vibrant home for unfiltered adult self-expression, photography, literature, and visual culture. Unlike sanitized corporate platforms governed by opaque algorithms, Nina.Sucks gives creators complete ownership of their content, customizable appearance, and an open reverse-chronological feed.</p>
      <h2>Unlimited Blogs for Creators</h2>
      <p>Every account holder can create unlimited independent blogs for distinct personas, projects, or genres. Customize accent palettes, banners, avatar artwork, and bio external links.</p>
      <h2>Creator Economy &amp; Community</h2>
      <p>Earn reward points simply by sharing, following, and referring peers. Boost your creations in our fair community ad marketplace or verify your identity for life.</p>
    `
  },
  'faq.php': {
    title: 'Frequently Asked Questions',
    lead: 'Everything you need to know about blogging, points, and platform features.',
    color: '#19e3ff',
    html: `
      <h2>How do multiple blogs work?</h2>
      <p>From your Creator Studio dashboard under "My Blogs", you can spin up unlimited additional blogs at any time. Each blog receives its own unique web address, title, theme color, avatar, and post archive.</p>
      <h2>How do reward points work?</h2>
      <p>You earn reward points by referring friends and through active community participation. Points can be spent in the Advertising Marketplace to boost your posts to thousands of members.</p>
      <h2>What are the Verified Creator badges?</h2>
      <p>Creators who support the community or verify their identity receive distinctive badges (PRO, ID Verified, VIP) unlocking enhanced visibility and reader trust.</p>
    `
  },
  'compliance.php': {
    title: '18 U.S.C. § 2257 Recordkeeping Compliance',
    lead: 'Mandatory age verification, operator notice, and legal recordkeeping statement.',
    color: '#ffd60a',
    html: `
      <h2>18 U.S.C. 2257 Notice</h2>
      <p>All visual depictions, images, and video clips displayed on Nina.Sucks depicting actual sexually explicit conduct are produced, uploaded, and maintained in compliance with the provisions of Title 18 U.S.C. Section 2257 and Section 2257A, as well as Part 75 of Title 28 of the Code of Federal Regulations.</p>
      <h2>Custodian of Records</h2>
      <p>The primary records custodian maintains records containing the primary identification documents verifying that all performers and content contributors depicted were at least 18 years of age at the time of content creation.</p>
    `
  },
  '2257.php': {
    title: '18 U.S.C. § 2257 Recordkeeping Compliance',
    lead: 'Mandatory age verification, operator notice, and legal recordkeeping statement.',
    color: '#ffd60a',
    html: `
      <h2>18 U.S.C. 2257 Notice</h2>
      <p>All visual depictions, images, and video clips displayed on Nina.Sucks depicting actual sexually explicit conduct are produced, uploaded, and maintained in compliance with the provisions of Title 18 U.S.C. Section 2257 and Section 2257A, as well as Part 75 of Title 28 of the Code of Federal Regulations.</p>
      <h2>Custodian of Records</h2>
      <p>The primary records custodian maintains records containing the primary identification documents verifying that all performers and content contributors depicted were at least 18 years of age at the time of content creation.</p>
    `
  },
  'safety.php': {
    title: 'Trust, Safety &amp; Community Guidelines',
    lead: 'Our zero-tolerance standards for consent, age compliance, and creator protection.',
    color: '#ff1f4b',
    html: `
      <h2>Zero Tolerance Policies</h2>
      <p>We enforce strict, non-negotiable zero tolerance for non-consensual content, CSAM/underage material, doxxing, harassment, and unauthorized distribution. Any violations result in immediate permanent account termination and referral to legal authorities.</p>
      <h2>Reporting Violations</h2>
      <p>Every post includes a quick flag/report button. Our moderation team monitors reports continuously to ensure platform integrity and creator safety.</p>
    `
  },
  'contact.php': {
    title: 'Contact Us',
    lead: 'Questions, DMCA notices, verification requests, or general support.',
    color: '#19e3ff',
    html: `<p>Please fill out the form below. We usually respond within 24 to 48 hours.</p>`,
    isContact: true
  }
};

Object.entries(legalPages).forEach(([route, page]) => {
  app.get(['/' + route, '/' + route.replace('.php', '')], (req: Request, res: Response) => {
    const flash = (req.session as any)?.flash;
    res.render('content-page', {
      page_title: `${page.title} | Nina.Sucks`,
      page_desc: page.lead,
      page_canonical: `http://localhost:3000/${route}`,
      active_nav: '',
      hero: { title: page.title, lead: page.lead, color: page.color },
      bodyHtml: page.html,
      isContact: Boolean(page.isContact),
      successMessage: flash?.success || null
    });
  });
});

// 9. Dashboard Routes
app.get(['/dashboard', '/dashboard/index.php', '/dashboard/posts.php'], (req: Request, res: Response) => {
  const currentUser = (req.session as any)?.user;
  if (!currentUser) return res.redirect('/login.php');

  const myPosts = posts.filter(p => p.blog.toLowerCase() === currentUser.username.toLowerCase());
  const totalLikes = myPosts.reduce((sum, p) => sum + (p.like_count || 0), 0);
  const myBlog = blogs.get(currentUser.username.toLowerCase()) || {
    id: 1,
    userId: currentUser.id,
    slug: currentUser.username,
    title: currentUser.username,
    tagline: '',
    color: '#ff2bd6',
    avatar_path: '',
    cover_path: 'hero.jpg',
    is_featured: 1,
    bio: '',
    badge: currentUser.badge
  };

  const isPostsView = req.path.includes('posts');

  res.render('dashboard', {
    page_title: 'Dashboard | Nina.Sucks',
    page_desc: 'Manage your Nina.Sucks blog, posts, and profile.',
    page_canonical: 'http://localhost:3000/dashboard',
    active_nav: 'dashboard',
    title: isPostsView ? 'Manage Posts' : 'Dashboard Overview',
    section: isPostsView ? 'posts' : 'overview',
    myPosts,
    totalLikes,
    followerCount: 42,
    myBlog,
    flashMsg: (req.session as any)?.flash?.dashboard || null
  });
});

app.get(['/dashboard/post.php', '/dashboard/post'], (req: Request, res: Response) => {
  const currentUser = (req.session as any)?.user;
  if (!currentUser) return res.redirect('/login.php');

  res.render('dashboard', {
    page_title: 'Create Post | Nina.Sucks',
    page_desc: 'Publish a new post to your blog.',
    page_canonical: 'http://localhost:3000/dashboard/post.php',
    active_nav: 'dashboard',
    title: 'Create Post',
    section: 'new_post',
    myPosts: [],
    totalLikes: 0,
    followerCount: 0,
    myBlog: blogs.get(currentUser.username.toLowerCase())
  });
});

app.post(['/dashboard/post.php', '/dashboard/post'], (req: Request, res: Response) => {
  const currentUser = (req.session as any)?.user;
  if (!currentUser) return res.redirect('/login.php');

  const { type, heading, body, caption, quote, source, url: linkUrl, chat, tags } = req.body;
  const postType = (type || 'text') as Post['type'];

  const tagList = tags
    ? String(tags).split(',').map(t => t.trim().toLowerCase()).filter(Boolean)
    : ['updates'];

  const chatLines: [string, string][] = [];
  if (postType === 'chat' && chat) {
    const lines = String(chat).split('\n');
    for (const line of lines) {
      const parts = line.split(':');
      if (parts.length >= 2) {
        chatLines.push([parts[0].trim(), parts.slice(1).join(':').trim()]);
      }
    }
  }

  const newPost: Post = {
    id: nextPostId++,
    blog: currentUser.username,
    blog_id: currentUser.id,
    color: '#ff2bd6',
    badge: currentUser.badge,
    avatar: '',
    type: postType,
    heading: heading ? String(heading) : undefined,
    body: body ? String(body) : undefined,
    caption: caption ? String(caption) : undefined,
    quote: quote ? String(quote) : undefined,
    source: source ? String(source) : undefined,
    title: heading ? String(heading) : (linkUrl ? 'Interesting Link' : undefined),
    domain: linkUrl ? new URL(String(linkUrl)).hostname : undefined,
    url: linkUrl ? String(linkUrl) : undefined,
    lines: chatLines.length ? chatLines : undefined,
    img: (postType === 'photo' || postType === 'video') ? 'hero.jpg' : undefined,
    tags: tagList,
    like_count: 0,
    reblog_count: 0,
    minutes: 0,
    createdAt: Date.now()
  };

  posts.unshift(newPost);
  res.redirect(`/post.php?id=${newPost.id}`);
});

app.get(['/dashboard/blogs.php', '/dashboard/blogs'], (req: Request, res: Response) => {
  const currentUser = (req.session as any)?.user;
  if (!currentUser) return res.redirect('/login.php');

  const myBlog = blogs.get(currentUser.username.toLowerCase()) || {
    id: 1,
    userId: currentUser.id,
    slug: currentUser.username,
    title: currentUser.username,
    tagline: '',
    color: '#ff2bd6',
    avatar_path: '',
    cover_path: 'hero.jpg',
    is_featured: 1,
    bio: '',
    badge: currentUser.badge
  };

  res.render('dashboard', {
    page_title: 'Blog Settings | Nina.Sucks',
    page_desc: 'Customize blog colors, bio, and title.',
    page_canonical: 'http://localhost:3000/dashboard/blogs.php',
    active_nav: 'dashboard',
    title: 'Blog Appearance',
    section: 'blogs',
    myPosts: [],
    totalLikes: 0,
    followerCount: 0,
    myBlog,
    flashMsg: (req.session as any)?.flash?.blogs || null
  });
});

app.post(['/dashboard/blogs.php', '/dashboard/blogs'], (req: Request, res: Response) => {
  const currentUser = (req.session as any)?.user;
  if (!currentUser) return res.redirect('/login.php');

  const { title, tagline, bio, color } = req.body;
  let blog = blogs.get(currentUser.username.toLowerCase());
  if (blog) {
    blog.title = String(title || blog.title);
    blog.tagline = String(tagline || '');
    blog.bio = String(bio || '');
    blog.color = String(color || '#ff2bd6');
  }

  (req.session as any).flash = { blogs: 'Blog settings updated successfully!' };
  res.redirect('/dashboard/blogs.php');
});

// 10. Admin Routes
app.get(['/admin', '/admin/index.php', '/admin/posts', '/admin/posts.php', '/admin/users', '/admin/users.php', '/admin/reports', '/admin/reports.php', '/admin/support', '/admin/support.php', '/admin/ads', '/admin/slider'], (req: Request, res: Response) => {
  let currentUser = (req.session as any)?.user;
  if (!currentUser || currentUser.role !== 'admin') {
    const admin = users.get('admin')!;
    (req.session as any).user = admin;
    currentUser = admin;
  }

  let section = (req.query.section as string) || 'overview';
  if (req.path.includes('posts')) section = 'posts';
  else if (req.path.includes('users')) section = 'users';
  else if (req.path.includes('reports')) section = 'reports';
  else if (req.path.includes('support')) section = 'support';
  else if (req.path.includes('ads')) section = 'ads';
  else if (req.path.includes('slider')) section = 'slider';

  res.render('admin', {
    page_title: 'Admin Moderation | Nina.Sucks',
    page_desc: 'Admin moderation panel',
    page_canonical: 'http://localhost:3000/admin',
    active_nav: 'admin',
    currentUser,
    title: section.charAt(0).toUpperCase() + section.slice(1),
    section,
    allPosts: posts,
    allUsers: [...users.values()],
    reports,
    tickets: supportTickets,
    allAds: advertisements,
    heroSlides: heroSlidesList,
    flashMsg: (req.session as any)?.flash?.admin || null
  });
});

app.post(['/admin/ads/create', '/admin/ads/create.php'], (req: Request, res: Response) => {
  const { title, sponsor_name, link_url, image_url, description, placement } = req.body;
  advertisements.push({
    id: nextAdId++,
    title: String(title || 'Sponsored Advertisement'),
    sponsor_name: String(sponsor_name || ''),
    link_url: String(link_url || 'https://nina.sucks'),
    image_url: String(image_url || 'hero.jpg'),
    description: String(description || ''),
    placement: String(placement || 'sidebar'),
    is_active: true
  });
  (req.session as any).flash = { admin: 'Advertisement created successfully!' };
  res.redirect('/admin?section=ads');
});

app.post(['/admin/ads/:id/delete', '/admin/ads/delete'], (req: Request, res: Response) => {
  const id = parseInt(String(req.params.id || req.body.id || '0'), 10);
  advertisements = advertisements.filter(a => a.id !== id);
  (req.session as any).flash = { admin: 'Advertisement deleted.' };
  res.redirect('/admin?section=ads');
});

app.post(['/admin/ads/:id/toggle', '/admin/ads/toggle'], (req: Request, res: Response) => {
  const id = parseInt(String(req.params.id || req.body.id || '0'), 10);
  const ad = advertisements.find(a => a.id === id);
  if (ad) ad.is_active = !ad.is_active;
  (req.session as any).flash = { admin: 'Advertisement status updated.' };
  res.redirect('/admin?section=ads');
});

app.post(['/admin/slider/add', '/admin/slider/add.php'], (req: Request, res: Response) => {
  const { title, text, img, color, cta1_label, cta1_url, cta2_label, cta2_url } = req.body;
  heroSlidesList.push({
    color: String(color || '#ff2bd6'),
    img: String(img || 'hero.jpg'),
    alt: String(title || 'Hero Slide'),
    x: 50, y: 30, z: 1.05, mx: 50, my: 25, mz: 1.15,
    title: String(title || 'New Slide'),
    text: String(text || ''),
    cta: [
      { label: String(cta1_label || 'Create blog'), href: String(cta1_url || 'register.php'), class: 'btn-fill' },
      { label: String(cta2_label || 'Explore feed'), href: String(cta2_url || '#feed'), class: 'btn-ghost' }
    ]
  });
  (req.session as any).flash = { admin: 'New slide added to hero slider!' };
  res.redirect('/admin?section=slider');
});

app.post(['/admin/slider/:id/delete', '/admin/slider/delete'], (req: Request, res: Response) => {
  const idx = parseInt(String(req.params.id || req.body.id || '0'), 10);
  if (heroSlidesList[idx]) {
    heroSlidesList.splice(idx, 1);
  }
  (req.session as any).flash = { admin: 'Slide deleted from hero slider.' };
  res.redirect('/admin?section=slider');
});

// 11. Remaining unmigrated PHP endpoints fallback
app.all('*.php', (req: Request, res: Response) => {
  res.status(501).json({ error: 'Endpoint not yet migrated', path: req.path });
});

// 404 handler
app.use((req: Request, res: Response) => {
  res.status(404).render('content-page', {
    page_title: 'Page Not Found | Nina.Sucks',
    page_desc: 'The requested page could not be found.',
    page_canonical: 'http://localhost:3000/404',
    active_nav: '',
    hero: { title: '404 - Not Found', lead: 'The page you were looking for does not exist.', color: '#ff1f4b' },
    bodyHtml: '<p>The page or resource you requested is missing or has been moved. <a class="lnk" href="/">Return to Explore</a></p>'
  });
});

// Start server
app.listen(PORT, '0.0.0.0', () => {
  console.log(`Nina.Sucks server is running on http://0.0.0.0:${PORT}`);
});
export default app;
