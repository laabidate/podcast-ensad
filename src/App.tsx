import { useState, useRef } from "react";

const NAV_ITEMS = {
  browse: [
    { id: "home", label: "Home", icon: HomeIcon },
    { id: "playlist", label: "Playlist", icon: PlaylistIcon },
    { id: "artist", label: "Artist", icon: ArtistIcon },
    { id: "albums", label: "Albums", icon: AlbumsIcon },
  ],
  discover: [
    { id: "radio", label: "Radio", icon: RadioIcon },
    { id: "event", label: "Event", icon: EventIcon },
    { id: "podcast", label: "Podcast", icon: PodcastIcon },
    { id: "foryou", label: "For You", icon: HeartIcon },
  ],
};

const TOP_MUSIC = [
  {
    id: 1,
    title: "FEFE",
    artist: "6ix9ine, Nicki Minaj",
    cover: "https://images.unsplash.com/photo-1614613535308-eb5fbd3d2c17?w=300&h=300&fit=crop&auto=format",
    bg: "#e8c4d8",
  },
  {
    id: 2,
    title: "Invasion of Privacy",
    artist: "Cardi B",
    cover: "https://images.unsplash.com/photo-1598387993441-a364f854c3e1?w=300&h=300&fit=crop&auto=format",
    bg: "#2d2d2d",
  },
  {
    id: 3,
    title: "Culture II",
    artist: "Migos",
    cover: "https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?w=300&h=300&fit=crop&auto=format",
    bg: "#c8a882",
  },
  {
    id: 4,
    title: "Boss",
    artist: "lil pump",
    cover: "https://images.unsplash.com/photo-1571974599782-87624638275e?w=300&h=300&fit=crop&auto=format",
    bg: "#f4a0b0",
  },
];

const POPULAR_TRACKS = [
  {
    id: 1,
    title: "Available (Nature Visual)",
    artist: "Justin Bieber",
    duration: "4:12",
    cover: "https://images.unsplash.com/photo-1618160702438-9b02ab6515c9?w=60&h=60&fit=crop&auto=format",
  },
  {
    id: 2,
    title: "GOOBA (Official Music)",
    artist: "6IX9INE",
    duration: "2:34",
    cover: "https://images.unsplash.com/photo-1614613535308-eb5fbd3d2c17?w=60&h=60&fit=crop&auto=format",
    playing: true,
  },
  {
    id: 3,
    title: "Memories",
    artist: "DMnet Music",
    duration: "4:54",
    cover: "https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?w=60&h=60&fit=crop&auto=format",
  },
];

const RECOMMENDED_ALBUMS = [
  {
    id: 1,
    title: "Lights",
    artist: "Migos",
    cover: "https://images.unsplash.com/photo-1619983081563-430f63602796?w=200&h=200&fit=crop&auto=format",
  },
  {
    id: 2,
    title: "Different World",
    artist: "Alan Walker",
    cover: "https://images.unsplash.com/photo-1519677584237-752f8853252e?w=200&h=200&fit=crop&auto=format",
  },
  {
    id: 3,
    title: "Different World",
    artist: "Alan Walker",
    cover: "https://images.unsplash.com/photo-1535992165812-68d1861aa71e?w=200&h=200&fit=crop&auto=format",
  },
];

export default function App() {
  const [activeNav, setActiveNav] = useState("home");
  const [playing, setPlaying] = useState(true);
  const [liked, setLiked] = useState<Set<number>>(new Set());
  const [progress, setProgress] = useState(48);
  const [currentTrack] = useState(POPULAR_TRACKS[1]);

  const toggleLike = (id: number) => {
    setLiked((prev) => {
      const next = new Set(prev);
      next.has(id) ? next.delete(id) : next.add(id);
      return next;
    });
  };

  return (
    <div className="flex flex-col h-full bg-white" style={{ fontFamily: "'Inter', sans-serif" }}>
      <div className="flex flex-1 overflow-hidden">
        {/* Sidebar */}
        <aside className="w-52 flex-shrink-0 flex flex-col bg-white border-r border-gray-100 overflow-y-auto py-6">
          {/* Logo */}
          <div className="px-5 mb-8">
            <div className="flex items-center gap-2 mb-1">
              <div className="flex items-center gap-0.5">
                <div className="w-7 h-8 border-2 border-gray-800 flex items-center justify-center">
                  <span className="text-xs font-bold text-gray-800 leading-none">HB</span>
                </div>
                <div className="w-px h-8 bg-gray-800" />
                <div className="px-1.5 py-0.5 bg-blue-600 text-white text-xs font-bold tracking-wider">
                  ENSAD
                </div>
              </div>
            </div>
            <p className="text-[8px] text-gray-400 leading-tight uppercase tracking-wide">
              École Nationale Supérieure d'Art et de Design
            </p>
            <p className="text-[8px] text-gray-400 leading-tight uppercase tracking-wide">
              Université Hassan II de Casablanca
            </p>
          </div>

          {/* Browse */}
          <div className="px-5 mb-1">
            <p className="text-[11px] font-semibold text-gray-400 uppercase tracking-widest mb-2">Browse</p>
          </div>
          <nav className="mb-6">
            {NAV_ITEMS.browse.map(({ id, label, icon: Icon }) => (
              <button
                key={id}
                onClick={() => setActiveNav(id)}
                className={`w-full flex items-center gap-3 px-5 py-2.5 text-sm transition-colors relative ${
                  activeNav === id
                    ? "text-blue-600 font-medium bg-blue-50"
                    : "text-gray-500 hover:text-gray-800 hover:bg-gray-50"
                }`}
              >
                {activeNav === id && (
                  <span className="absolute left-0 top-0 bottom-0 w-0.5 bg-blue-600 rounded-r" />
                )}
                <Icon size={16} active={activeNav === id} />
                {label}
              </button>
            ))}
          </nav>

          {/* Discover */}
          <div className="px-5 mb-1">
            <p className="text-[11px] font-semibold text-gray-400 uppercase tracking-widest mb-2">Discover</p>
          </div>
          <nav>
            {NAV_ITEMS.discover.map(({ id, label, icon: Icon }) => (
              <button
                key={id}
                onClick={() => setActiveNav(id)}
                className={`w-full flex items-center gap-3 px-5 py-2.5 text-sm transition-colors relative ${
                  activeNav === id
                    ? "text-blue-600 font-medium bg-blue-50"
                    : "text-gray-500 hover:text-gray-800 hover:bg-gray-50"
                }`}
              >
                {activeNav === id && (
                  <span className="absolute left-0 top-0 bottom-0 w-0.5 bg-blue-600 rounded-r" />
                )}
                <Icon size={16} active={activeNav === id} />
                {label}
              </button>
            ))}
          </nav>
        </aside>

        {/* Main content */}
        <main className="flex-1 flex flex-col overflow-hidden">
          {/* Top bar */}
          <header className="flex items-center justify-between px-8 py-4 border-b border-gray-100 flex-shrink-0">
            <div className="flex items-center gap-2 flex-1 max-w-md">
              <SearchIcon />
              <input
                type="text"
                placeholder="Search for song, artists etc..."
                className="flex-1 text-sm text-gray-500 outline-none placeholder-gray-400 bg-transparent"
              />
            </div>
            <div className="flex items-center gap-4">
              <button className="text-gray-400 hover:text-gray-600 transition-colors">
                <SettingsIcon />
              </button>
              <button className="text-gray-400 hover:text-gray-600 transition-colors">
                <BellIcon />
              </button>
              <button className="px-5 py-2 text-sm font-medium text-blue-600 border border-blue-600 rounded-full hover:bg-blue-50 transition-colors">
                Upgrade To Premium
              </button>
            </div>
          </header>

          {/* Content area */}
          <div className="flex-1 overflow-y-auto px-8 py-6">
            {/* Top Music */}
            <section className="mb-8">
              <div className="flex items-center justify-between mb-4">
                <h2 className="text-lg font-bold text-gray-800">Top Music</h2>
                <div className="flex gap-2">
                  <button className="w-7 h-7 rounded-full border border-gray-200 flex items-center justify-center text-gray-400 hover:border-gray-400 transition-colors">
                    <ChevronLeft />
                  </button>
                  <button className="w-7 h-7 rounded-full border border-gray-200 flex items-center justify-center text-gray-400 hover:border-gray-400 transition-colors">
                    <ChevronRight />
                  </button>
                </div>
              </div>
              <div className="grid grid-cols-4 gap-4">
                {TOP_MUSIC.map((album) => (
                  <div key={album.id} className="group cursor-pointer">
                    <div className="relative rounded-xl overflow-hidden mb-2 aspect-square">
                      <img
                        src={album.cover}
                        alt={album.title}
                        className="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                      />
                      <div className="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors flex items-center justify-center">
                        <button className="opacity-0 group-hover:opacity-100 transition-opacity w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-lg">
                          <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                            <path d="M3 2l10 5-10 5V2z" fill="#2563eb" />
                          </svg>
                        </button>
                      </div>
                    </div>
                    <p className="text-sm font-semibold text-gray-800 truncate">{album.title}</p>
                    <p className="text-xs text-gray-400 truncate">{album.artist}</p>
                  </div>
                ))}
              </div>
            </section>

            {/* Bottom two-column grid */}
            <div className="grid grid-cols-2 gap-8">
              {/* Popular */}
              <section>
                <h2 className="text-lg font-bold text-gray-800 mb-4">Popular</h2>
                <div className="flex flex-col gap-2">
                  {POPULAR_TRACKS.map((track) => (
                    <div
                      key={track.id}
                      className={`flex items-center gap-3 px-3 py-3 rounded-xl transition-colors ${
                        track.playing ? "bg-gray-50 shadow-sm" : "hover:bg-gray-50"
                      }`}
                    >
                      <button className="w-8 h-8 flex items-center justify-center flex-shrink-0">
                        {track.playing ? (
                          <div className="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                            <PauseIcon color="white" size={12} />
                          </div>
                        ) : (
                          <PlayCircleIcon />
                        )}
                      </button>
                      <img
                        src={track.cover}
                        alt={track.title}
                        className="w-10 h-10 rounded-lg object-cover flex-shrink-0"
                      />
                      <div className="flex-1 min-w-0">
                        <p className="text-sm font-medium text-gray-800 truncate">{track.title}</p>
                        <p className="text-xs text-gray-400 truncate">{track.artist}</p>
                      </div>
                      <span className="text-xs text-gray-400 flex-shrink-0">{track.duration}</span>
                      <button
                        onClick={() => toggleLike(track.id)}
                        className="flex-shrink-0 transition-colors"
                      >
                        <HeartSmall filled={liked.has(track.id)} />
                      </button>
                    </div>
                  ))}
                </div>
              </section>

              {/* Recommended Album */}
              <section>
                <div className="flex items-center justify-between mb-4">
                  <h2 className="text-lg font-bold text-gray-800">Recommended Album</h2>
                  <div className="flex gap-2">
                    <button className="w-7 h-7 rounded-full border border-gray-200 flex items-center justify-center text-gray-400 hover:border-gray-400 transition-colors">
                      <ChevronLeft />
                    </button>
                    <button className="w-7 h-7 rounded-full border border-gray-200 flex items-center justify-center text-gray-400 hover:border-gray-400 transition-colors">
                      <ChevronRight />
                    </button>
                  </div>
                </div>
                <div className="grid grid-cols-3 gap-3">
                  {RECOMMENDED_ALBUMS.map((album) => (
                    <div key={album.id} className="group cursor-pointer">
                      <div className="relative rounded-xl overflow-hidden mb-2 aspect-square bg-gray-900">
                        <img
                          src={album.cover}
                          alt={album.title}
                          className="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition-opacity"
                        />
                      </div>
                      <p className="text-sm font-semibold text-gray-800 truncate">{album.title}</p>
                      <p className="text-xs text-gray-400 truncate">{album.artist}</p>
                    </div>
                  ))}
                </div>
              </section>
            </div>
          </div>
        </main>
      </div>

      {/* Bottom player */}
      <div className="flex-shrink-0 flex items-center gap-6 px-6 py-3 bg-white border-t border-gray-100 shadow-[0_-1px_12px_rgba(0,0,0,0.06)]">
        {/* Track info */}
        <div className="flex items-center gap-3 w-52 flex-shrink-0">
          <img
            src={currentTrack.cover}
            alt={currentTrack.title}
            className="w-11 h-11 rounded-lg object-cover"
          />
          <div className="min-w-0">
            <p className="text-sm font-semibold text-gray-800 truncate">{currentTrack.title}</p>
            <p className="text-xs text-gray-400 truncate">{currentTrack.artist}</p>
          </div>
        </div>

        {/* Controls + progress */}
        <div className="flex-1 flex flex-col items-center gap-1.5">
          <div className="flex items-center gap-5">
            <button className="text-gray-400 hover:text-gray-600 transition-colors">
              <SkipBackIcon />
            </button>
            <button
              onClick={() => setPlaying(!playing)}
              className="w-9 h-9 bg-blue-600 rounded-full flex items-center justify-center hover:bg-blue-700 transition-colors shadow-md"
            >
              {playing ? <PauseIcon color="white" size={14} /> : <PlayIcon color="white" size={14} />}
            </button>
            <button className="text-gray-400 hover:text-gray-600 transition-colors">
              <SkipForwardIcon />
            </button>
          </div>
          <div className="flex items-center gap-3 w-full max-w-sm">
            <span className="text-xs text-gray-400 w-8 text-right">4:01</span>
            <div className="flex-1 relative">
              <input
                type="range"
                min={0}
                max={100}
                value={progress}
                onChange={(e) => setProgress(Number(e.target.value))}
                className="w-full"
                style={{
                  background: `linear-gradient(to right, #2563eb ${progress}%, #e5e7eb ${progress}%)`,
                }}
              />
            </div>
            <span className="text-xs text-gray-400 w-8">4:54</span>
          </div>
        </div>

        {/* Right controls */}
        <div className="flex items-center gap-4 w-32 justify-end flex-shrink-0">
          <button
            onClick={() => toggleLike(currentTrack.id)}
            className="text-gray-400 hover:text-red-500 transition-colors"
          >
            <HeartSmall filled={liked.has(currentTrack.id)} />
          </button>
          <button className="text-gray-400 hover:text-gray-600 transition-colors">
            <ShareIcon />
          </button>
          <button className="text-gray-400 hover:text-gray-600 transition-colors">
            <RepeatIcon />
          </button>
        </div>
      </div>
    </div>
  );
}

// ── Icons ─────────────────────────────────────────────────────────────────────

function HomeIcon({ size = 16, active = false }: { size?: number; active?: boolean }) {
  return (
    <svg width={size} height={size} viewBox="0 0 16 16" fill="none">
      <path
        d="M2 6.5L8 2l6 4.5V14H10v-3H6v3H2V6.5z"
        stroke={active ? "#2563eb" : "#9ca3af"}
        strokeWidth="1.5"
        strokeLinejoin="round"
      />
    </svg>
  );
}

function PlaylistIcon({ size = 16, active = false }: { size?: number; active?: boolean }) {
  return (
    <svg width={size} height={size} viewBox="0 0 16 16" fill="none">
      <path d="M2 4h12M2 8h8M2 12h6" stroke={active ? "#2563eb" : "#9ca3af"} strokeWidth="1.5" strokeLinecap="round" />
      <circle cx="13" cy="11" r="2.5" stroke={active ? "#2563eb" : "#9ca3af"} strokeWidth="1.5" />
    </svg>
  );
}

function ArtistIcon({ size = 16, active = false }: { size?: number; active?: boolean }) {
  return (
    <svg width={size} height={size} viewBox="0 0 16 16" fill="none">
      <circle cx="8" cy="5" r="3" stroke={active ? "#2563eb" : "#9ca3af"} strokeWidth="1.5" />
      <path d="M2 14c0-3 2.686-4 6-4s6 1 6 4" stroke={active ? "#2563eb" : "#9ca3af"} strokeWidth="1.5" strokeLinecap="round" />
    </svg>
  );
}

function AlbumsIcon({ size = 16, active = false }: { size?: number; active?: boolean }) {
  return (
    <svg width={size} height={size} viewBox="0 0 16 16" fill="none">
      <rect x="2" y="2" width="12" height="12" rx="6" stroke={active ? "#2563eb" : "#9ca3af"} strokeWidth="1.5" />
      <circle cx="8" cy="8" r="2" stroke={active ? "#2563eb" : "#9ca3af"} strokeWidth="1.5" />
    </svg>
  );
}

function RadioIcon({ size = 16, active = false }: { size?: number; active?: boolean }) {
  return (
    <svg width={size} height={size} viewBox="0 0 16 16" fill="none">
      <circle cx="8" cy="9" r="5" stroke={active ? "#2563eb" : "#9ca3af"} strokeWidth="1.5" />
      <path d="M4 4L12 2" stroke={active ? "#2563eb" : "#9ca3af"} strokeWidth="1.5" strokeLinecap="round" />
      <circle cx="8" cy="9" r="1.5" fill={active ? "#2563eb" : "#9ca3af"} />
    </svg>
  );
}

function EventIcon({ size = 16, active = false }: { size?: number; active?: boolean }) {
  return (
    <svg width={size} height={size} viewBox="0 0 16 16" fill="none">
      <rect x="2" y="3" width="12" height="11" rx="1.5" stroke={active ? "#2563eb" : "#9ca3af"} strokeWidth="1.5" />
      <path d="M5 2v2M11 2v2M2 7h12" stroke={active ? "#2563eb" : "#9ca3af"} strokeWidth="1.5" strokeLinecap="round" />
    </svg>
  );
}

function PodcastIcon({ size = 16, active = false }: { size?: number; active?: boolean }) {
  return (
    <svg width={size} height={size} viewBox="0 0 16 16" fill="none">
      <circle cx="8" cy="6" r="2.5" stroke={active ? "#2563eb" : "#9ca3af"} strokeWidth="1.5" />
      <path d="M5 6a3 3 0 006 0" stroke={active ? "#2563eb" : "#9ca3af"} strokeWidth="1.5" strokeLinecap="round" />
      <path d="M3 6a5 5 0 0010 0" stroke={active ? "#2563eb" : "#9ca3af"} strokeWidth="1.5" strokeLinecap="round" />
      <path d="M8 11v3" stroke={active ? "#2563eb" : "#9ca3af"} strokeWidth="1.5" strokeLinecap="round" />
    </svg>
  );
}

function HeartIcon({ size = 16, active = false }: { size?: number; active?: boolean }) {
  return (
    <svg width={size} height={size} viewBox="0 0 16 16" fill="none">
      <path
        d="M8 13.5S2 9.5 2 5.5A3.5 3.5 0 018 3.5 3.5 3.5 0 0114 5.5C14 9.5 8 13.5 8 13.5z"
        stroke={active ? "#2563eb" : "#9ca3af"}
        strokeWidth="1.5"
        strokeLinejoin="round"
      />
    </svg>
  );
}

function HeartSmall({ filled }: { filled: boolean }) {
  return (
    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
      <path
        d="M8 13.5S2 9.5 2 5.5A3.5 3.5 0 018 3.5 3.5 3.5 0 0114 5.5C14 9.5 8 13.5 8 13.5z"
        stroke={filled ? "#ef4444" : "#9ca3af"}
        fill={filled ? "#ef4444" : "none"}
        strokeWidth="1.5"
        strokeLinejoin="round"
      />
    </svg>
  );
}

function SearchIcon() {
  return (
    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
      <circle cx="7" cy="7" r="4.5" stroke="#9ca3af" strokeWidth="1.5" />
      <path d="M10.5 10.5L14 14" stroke="#9ca3af" strokeWidth="1.5" strokeLinecap="round" />
    </svg>
  );
}

function SettingsIcon() {
  return (
    <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
      <circle cx="9" cy="9" r="2.5" stroke="#9ca3af" strokeWidth="1.5" />
      <path
        d="M9 1v2M9 15v2M1 9h2M15 9h2M3.05 3.05l1.41 1.41M13.54 13.54l1.41 1.41M14.95 3.05l-1.41 1.41M4.46 13.54l-1.41 1.41"
        stroke="#9ca3af"
        strokeWidth="1.5"
        strokeLinecap="round"
      />
    </svg>
  );
}

function BellIcon() {
  return (
    <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
      <path
        d="M9 2a5 5 0 00-5 5v3l-1.5 2H15.5L14 10V7a5 5 0 00-5-5z"
        stroke="#9ca3af"
        strokeWidth="1.5"
        strokeLinejoin="round"
      />
      <path d="M7.5 15a1.5 1.5 0 003 0" stroke="#9ca3af" strokeWidth="1.5" strokeLinecap="round" />
    </svg>
  );
}

function ChevronLeft() {
  return (
    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
      <path d="M7.5 2L4 6l3.5 4" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
    </svg>
  );
}

function ChevronRight() {
  return (
    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
      <path d="M4.5 2L8 6l-3.5 4" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
    </svg>
  );
}

function PlayCircleIcon() {
  return (
    <svg width="32" height="32" viewBox="0 0 32 32" fill="none">
      <circle cx="16" cy="16" r="15" stroke="#e5e7eb" strokeWidth="1.5" />
      <path d="M13 11l9 5-9 5V11z" fill="#9ca3af" />
    </svg>
  );
}

function PauseIcon({ color = "#374151", size = 16 }: { color?: string; size?: number }) {
  return (
    <svg width={size} height={size} viewBox="0 0 16 16" fill="none">
      <rect x="4" y="3" width="3" height="10" rx="1" fill={color} />
      <rect x="9" y="3" width="3" height="10" rx="1" fill={color} />
    </svg>
  );
}

function PlayIcon({ color = "#374151", size = 16 }: { color?: string; size?: number }) {
  return (
    <svg width={size} height={size} viewBox="0 0 16 16" fill="none">
      <path d="M4 3l10 5-10 5V3z" fill={color} />
    </svg>
  );
}

function SkipBackIcon() {
  return (
    <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
      <path d="M4 3v12M14 3L6 9l8 6V3z" stroke="#9ca3af" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
    </svg>
  );
}

function SkipForwardIcon() {
  return (
    <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
      <path d="M14 3v12M4 3l8 6-8 6V3z" stroke="#9ca3af" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
    </svg>
  );
}

function ShareIcon() {
  return (
    <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
      <circle cx="14" cy="4" r="2" stroke="#9ca3af" strokeWidth="1.5" />
      <circle cx="14" cy="14" r="2" stroke="#9ca3af" strokeWidth="1.5" />
      <circle cx="4" cy="9" r="2" stroke="#9ca3af" strokeWidth="1.5" />
      <path d="M6 8l6-3M6 10l6 3" stroke="#9ca3af" strokeWidth="1.5" />
    </svg>
  );
}

function RepeatIcon() {
  return (
    <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
      <path
        d="M3 7V5a1 1 0 011-1h10l-2-2M15 11v2a1 1 0 01-1 1H4l2 2"
        stroke="#9ca3af"
        strokeWidth="1.5"
        strokeLinecap="round"
        strokeLinejoin="round"
      />
    </svg>
  );
}
