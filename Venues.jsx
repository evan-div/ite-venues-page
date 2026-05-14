// Page header — interior page header replacing the homepage hero
const VenuesHeader = () => (
  <header className="page-header">
    <div className="container">
      <div className="breadcrumb">
        <a href="/">Home</a>
        <span className="sep">/</span>
        <span>Venues</span>
      </div>
      <h1>Venues we <span className="grad">know inside out</span>.</h1>
      <p className="lead">
        We have rigged, staged, and floraled every room on this list — most of them many times over.
        Pick the venue you love, and we'll bring the production playbook with us.
      </p>
      <div className="meta">
        <div className="item">
          <div className="l">Curated</div>
          <div className="v">13 Venues</div>
        </div>
        <div className="item">
          <div className="l">Coverage</div>
          <div className="v">Utah · Wyoming · Nationwide</div>
        </div>
        <div className="item">
          <div className="l">Established</div>
          <div className="v">2007 · ITE</div>
        </div>
      </div>
    </div>
  </header>
);

// Filter bar — sticks under the nav
const FILTERS = [
  { id: 'all',          label: 'All Venues' },
  { id: 'slc',          label: 'Salt Lake City' },
  { id: 'utah-county',  label: 'Utah County' },
  { id: 'mountain',     label: 'Mountain Resorts' },
];

const FilterBar = ({ active, setActive, total }) => {
  const counts = React.useMemo(() => {
    const map = { all: window.VENUES.length };
    window.VENUES.forEach((v) => { map[v.region] = (map[v.region] || 0) + 1; });
    return map;
  }, []);
  return (
    <div className="filter-bar">
      <div className="container">
        <div className="row">
          <div className="chips">
            {FILTERS.map((f) => (
              <button
                key={f.id}
                className={`chip ${active === f.id ? 'active' : ''}`}
                onClick={() => setActive(f.id)}
              >
                {f.label}
                <span className="ct">{counts[f.id] || 0}</span>
              </button>
            ))}
          </div>
          <div className="count">
            Showing <strong>{total}</strong> of <strong>{window.VENUES.length}</strong>
          </div>
        </div>
      </div>
    </div>
  );
};

// Featured venue — large two-column card
const FeaturedVenue = ({ v, onView }) => {
  const [activeIdx, setActiveIdx] = React.useState(0);
  const photo = `assets/photos/${v.galleryStyles[activeIdx]}`;
  // first thumb shows featured photo
  return (
    <section className="featured-section">
      <div className="container">
        <div className="featured-card">
          <div className="photo" style={{ backgroundImage: `url(${photo})` }}>
            <div className="overlay" />
            <div className="badge">
              <i data-lucide="star" />
              Featured Partner
            </div>
            <div className="thumbs">
              {v.galleryStyles.map((g, i) => (
                <div
                  key={g}
                  className={`thumb ${i === activeIdx ? 'active' : ''}`}
                  style={{ backgroundImage: `url(assets/photos/${g})` }}
                  onClick={() => setActiveIdx(i)}
                />
              ))}
            </div>
          </div>
          <div className="body">
            <div className="eyebrow">Venue Spotlight</div>
            <div className="heading-hairline" />
            <h2>{v.name}</h2>
            <div className="desc">{v.desc}</div>
            <div className="specs">
              <div className="spec">
                <div className="l"><i data-lucide="map-pin" />Location</div>
                <div className="v">{v.city}</div>
              </div>
              <div className="spec">
                <div className="l"><i data-lucide="users" />Capacity</div>
                <div className="v">{v.capacity} guests</div>
              </div>
              <div className="spec">
                <div className="l"><i data-lucide="ruler" />Footprint</div>
                <div className="v">{v.sqft}</div>
              </div>
              <div className="spec">
                <div className="l"><i data-lucide="move-vertical" />Ceiling</div>
                <div className="v">{v.ceiling}</div>
              </div>
            </div>
            <div className="actions">
              <Button kind="gradient" onClick={() => onView(v)}>View Venue</Button>
              <Button kind="ghost">Book a Walk-Through →</Button>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
};

// Single venue card
const VenueCard = ({ v, onClick }) => (
  <div
    className="venue-card"
    onClick={onClick}
    onKeyDown={(e) => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); onClick(); } }}
    tabIndex={0}
    role="button"
    aria-label={`View ${v.name}`}
  >
    <div className="media" style={{ backgroundImage: `url(${v.photo})` }}>
      <div className="city-tag">{v.city}</div>
      <div className="cap-tag">
        <i data-lucide="users" />
        Up to {v.capacity}
      </div>
    </div>
    <div className="info">
      <h3 className="name">{v.name}</h3>
      <div className="address">
        <i data-lucide="map-pin" />
        <span>{v.address}</span>
      </div>
      <div className="row-bottom">
        <span className="style">{v.style}</span>
        <span className="view">
          View Venue
          <i data-lucide="arrow-right" />
        </span>
      </div>
    </div>
  </div>
);

// Venues grid (filtered)
const VenuesGrid = ({ list, onSelect }) => (
  <section className="venues-section">
    <div className="container">
      <div className="section-head">
        <div>
          <Eyebrow>The Full List</Eyebrow>
          <HeadingHairline />
          <h2 className="display m">All venues</h2>
        </div>
        <p className="body-l" style={{ margin: 0, maxWidth: '36ch', fontSize: 14 }}>
          Sorted by frequency of recent ITE production work. Don't see your venue? We work nationwide.
        </p>
      </div>
      <div className="venues-grid">
        {list.map((v) => (
          <VenueCard key={v.id} v={v} onClick={() => onSelect(v)} />
        ))}
      </div>
    </div>
  </section>
);

// CTA band
const VenueCTA = () => (
  <section className="cta-band">
    <div className="container">
      <div className="inner">
        <div>
          <Eyebrow>Don't See Your Venue?</Eyebrow>
          <HeadingHairline />
          <h2>We travel the production with you.</h2>
          <p>
            We've built shows in 30+ states. Tell us where, and our team will bring the rig,
            the rentals, and the crew. Out-of-state or out-of-country logistics flow through our
            sister company.
          </p>
          <div className="actions" style={{ marginTop: 32 }}>
            <Button kind="gradient">Start a Project</Button>
            <Button kind="ghost" style={{ color: '#C7DA84' }}>Talk to an Account Lead →</Button>
          </div>
          <div className="sister">
            International? <a>Visit Business Jaunt →</a>
          </div>
        </div>
        <div style={{
          padding: 32,
          border: '1px solid rgba(255,255,255,0.18)',
          borderRadius: 4,
          background: 'rgba(255,255,255,0.04)'
        }}>
          <div className="eyebrow" style={{ color: '#C7DA84' }}>Quick Inquiry</div>
          <div className="heading-hairline" style={{ background: 'rgba(255,255,255,0.3)' }} />
          <h3 style={{
            fontFamily: 'var(--font-display)',
            fontWeight: 500,
            fontSize: 22,
            letterSpacing: '0.10em',
            textTransform: 'uppercase',
            margin: '0 0 20px',
            color: '#fff',
          }}>
            Tell us about your event
          </h3>
          <div style={{ display: 'flex', flexDirection: 'column', gap: 14 }}>
            {[
              ['calendar', 'Event date'],
              ['users', 'Estimated guest count'],
              ['map-pin', 'Preferred venue or city'],
            ].map(([icon, label]) => (
              <div key={label} style={{
                display: 'flex',
                alignItems: 'center',
                gap: 12,
                padding: '12px 14px',
                background: 'rgba(255,255,255,0.06)',
                border: '1px solid rgba(255,255,255,0.14)',
                borderRadius: 4,
                color: 'rgba(255,255,255,0.6)',
                fontFamily: 'var(--font-body)',
                fontSize: 14,
              }}>
                <i data-lucide={icon} style={{ width: 16, height: 16, color: '#C7DA84' }} />
                {label}
              </div>
            ))}
            <Button kind="gradient" style={{ marginTop: 4 }}>Request a Quote</Button>
          </div>
        </div>
      </div>
    </div>
  </section>
);

// Modal — venue detail
const VenueModal = ({ v, onClose }) => {
  React.useEffect(() => {
    const onKey = (e) => { if (e.key === 'Escape') onClose(); };
    if (v) window.addEventListener('keydown', onKey);
    return () => window.removeEventListener('keydown', onKey);
  }, [v, onClose]);

  return (
    <div
      className={`modal-backdrop ${v ? 'open' : ''}`}
      onClick={onClose}
      role="dialog"
      aria-modal="true"
      aria-label={v ? v.name : 'Venue detail'}
    >
      <div className="modal-wrap" onClick={(e) => e.stopPropagation()}>
        <button className="close" onClick={onClose} aria-label="Close">
          <i data-lucide="x" style={{ width: 16, height: 16 }} />
        </button>
        {v && (
          <div className="modal">
            <div className="ph" style={{ backgroundImage: `url(${v.photo})` }} />
            <div className="body">
              <div className="eyebrow">{v.style}</div>
              <h3>{v.name}</h3>
              <div className="addr">
                <i data-lucide="map-pin" />
                <span>{v.address}</span>
              </div>
              <p className="desc">{v.desc}</p>
              <div className="specs">
                <div className="spec"><div className="l">Capacity</div><div className="v">Up to {v.capacity} guests</div></div>
                <div className="spec"><div className="l">Footprint</div><div className="v">{v.sqft}</div></div>
                <div className="spec"><div className="l">Ceiling</div><div className="v">{v.ceiling}</div></div>
                <div className="spec"><div className="l">Region</div><div className="v">{v.city}</div></div>
              </div>
              <div className="actions">
                <Button kind="primary">Plan an Event Here</Button>
                <Button kind="ghost">Get Directions →</Button>
              </div>
            </div>
          </div>
        )}
      </div>
    </div>
  );
};

Object.assign(window, {
  VenuesHeader, FilterBar, FeaturedVenue, VenueCard, VenuesGrid, VenueCTA, VenueModal,
});
