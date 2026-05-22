// Primitives + Nav + Footer (inlined for the venues page)
const Eyebrow = ({ children }) => (<div className="eyebrow">{children}</div>);
const HeadingHairline = () => <div className="heading-hairline" />;
const SectionHeading = ({ eyebrow, title, size = 'm' }) => (
  <header>
    {eyebrow && <Eyebrow>{eyebrow}</Eyebrow>}
    <HeadingHairline />
    <h2 className={`display ${size}`}>{title}</h2>
  </header>
);
const Button = ({ kind = 'primary', children, onClick, href, style }) => {
  const Tag = href ? 'a' : 'button';
  return (
    <Tag className={`btn ${kind}`} onClick={onClick} href={href} style={style}>
      {children}
    </Tag>
  );
};

const NAV_ITEMS = [
  ['services', 'Services'],
  ['work', 'Work'],
  ['venues', 'Venues'],
  ['rentals', 'Rentals'],
  ['about', 'About'],
  ['contact', 'Contact'],
];

const Nav = ({ active = 'venues' }) => (
  <nav className="nav">
    <div className="brand">
      <img src="assets/logo-horizontal-official.svg" alt="In The Event" />
    </div>
    <div className="links">
      {NAV_ITEMS.map(([key, label]) => (
        <a key={key} className={active === key ? 'active' : ''}>{label}</a>
      ))}
    </div>
    <Button kind="primary">Start a project</Button>
  </nav>
);

const Footer = () => (
  <footer className="footer">
    <div className="container">
      <div className="cols">
        <div className="brand-block">
          <img src="assets/logo-stack-official.svg" alt="In The Event"
               style={{ filter: 'invert(1) brightness(1.15)', height: 120 }} />
          <p className="lead">
            Bridging creative design and technical execution, from Salt Lake City to events nationwide.
          </p>
        </div>
        <div>
          <h4>Services</h4>
          <ul>
            <li><a>Design</a></li><li><a>Production</a></li><li><a>Florals</a></li>
            <li><a>Audio &amp; Video</a></li><li><a>Rentals</a></li>
          </ul>
        </div>
        <div>
          <h4>Company</h4>
          <ul>
            <li><a>About</a></li><li><a>Work</a></li>
            <li><a>Venues</a></li><li><a>Contact</a></li>
          </ul>
        </div>
        <div>
          <h4>Contact</h4>
          <ul>
            <li><a href="mailto:info@intheevent.com">info@intheevent.com</a></li>
            <li><a href="tel:18018861144">801.886.1144</a></li>
            <li>3008 S 300 W</li>
            <li>Salt Lake City, UT 84115</li>
          </ul>
        </div>
      </div>
      <div className="fine">
        <div>© 2026 In The Event, LLC · All rights reserved</div>
        <div>Est. 2007 · ITE · Business Jaunt · Mannequin Rental Co.</div>
      </div>
    </div>
  </footer>
);

Object.assign(window, { Eyebrow, HeadingHairline, SectionHeading, Button, Nav, Footer });
