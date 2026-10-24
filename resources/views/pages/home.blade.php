@extends('layouts.app')

@section('content')


<div class="rb-progress" aria-hidden="true"></div>
<div class="rb-home">
    <!-- ================= HERO ================= -->
    <section class="rb-hero live-hero" aria-labelledby="rb-title">
        <div class="rb-shell rb-hero-shell">
            <div class="rb-hero-copy">
                <span class="rb-kicker">Roman Bahram Logistic Services</span>
                <h1 id="rb-title">
                    <span class="rb-line"><span>Moving</span></span>
                    <span class="rb-line"><span><em>Afghanistan</em></span></span>
                    <span class="rb-line"><span>forward.</span></span>
                </h1>
                <p class="rb-hero-lede">Procurement, transportation, vehicle rental, and field logistics coordinated by one accountable Afghan team.</p>
                <div class="rb-actions">
                    <a class="rb-btn rb-btn-red" href="{{ route('contact') }}" data-magnetic>
                        <span>Start a conversation</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
                    </a>
                    <a class="rb-btn rb-btn-line" href="{{ route('projects') }}" data-magnetic>
                        <span>View our work</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
                    </a>
                </div>
            </div>
            <!-- <aside class="rb-field-index" aria-label="RBLS field operations" data-tilt>
                <span class="rb-field-index-label">Kabul / Afghanistan</span>
                <p>Local knowledge.<br><em>Nationwide reach.</em></p>
                <span>Procurement · transport · field support</span>
            </aside> -->
            <div class="rb-hero-float-card one" aria-hidden="true"><div><strong>Field ready</strong><span>One accountable team</span></div></div>
            <div class="rb-hero-float-card two" aria-hidden="true"><div><strong>Built locally</strong><span>Reach across Afghanistan</span></div></div>
        </div>
        <span class="rb-hero-note">Kabul / Afghanistan / Since 2022</span>
        <div class="rb-scroll-hint">Scroll</div>
        <div class="rb-shell rb-hero-foot">
            <div><strong data-count="53">53</strong><span>Portfolio assignments</span></div>
            <div><strong data-count="15" data-prefix="AFN " data-suffix="M+">AFN 15M+</strong><span>Approx. annual turnover</span></div>
            <div><strong >81310</strong><span>Business license</span></div>
        </div>
    </section>

    <!-- ================= MARQUEE ================= -->
    <div class="rb-trust" aria-label="RBLS capabilities">
        <div class="rb-trust-track" aria-hidden="true">
            @for ($i = 0; $i < 2; $i++)
                <span>Procurement</span><span>Transportation</span><span>Vehicle rental</span>
                <span>Warehousing</span><span>Distribution</span><span>Field support</span>
            @endfor
        </div>
    </div>

    <!-- ================= INTRO ================= -->
    <section class="rb-intro" id="about-rbls">
        <div class="rb-shell rb-intro-grid">
            <div class="rb-intro-art rb-reveal">
                <div class="rb-intro-frame" aria-hidden="true"></div>
                <img class="rb-intro-image" src="{{ asset('images/rbls-field.png') }}" alt="RBLS field logistics support" loading="lazy">
                <div class="rb-intro-number"><strong>2022</strong><span>Established<br>in Kabul</span></div>
            </div>
            <div class="rb-intro-copy rb-reveal-right rb-reveal">
                <span class="rb-kicker">Built for the Afghan operating environment</span>
                <h2>Reliable systems behind important work.</h2>
                <p>RBLS is an Afghan-owned logistics and procurement company registered with the Ministry of Industry and Commerce under License No. 81310. We support NGOs, UN agencies, development organizations, businesses, and institutions with practical, documented, cost-conscious solutions.</p>
                <div class="rb-mini-grid">
                    <div><strong>Local network</strong><span>Suppliers, transport providers, and service partners.</span></div>
                    <div><strong>Clear accountability</strong><span>Communication and documentation at every stage.</span></div>
                </div>
                <a class="rb-btn rb-btn-dark" href="{{ route('about') }}" data-magnetic>
                    <span>Discover RBLS</span><span aria-hidden="true">↗</span>
                </a>
            </div>
        </div>
    </section>

    <!-- ================= CAPABILITIES ================= -->
    <section class="rb-capabilities" aria-labelledby="rb-capabilities-title">
        <div class="rb-shell">
            <div class="rb-section-head rb-reveal">
                <div>
                    <span class="rb-kicker">What we deliver</span>
                    <h2 id="rb-capabilities-title">The whole chain.<br>One partner.</h2>
                </div>
                <p>From requirement understanding to final delivery, RBLS coordinates the practical details behind successful projects.</p>
            </div>
            <div class="rb-cap-grid rb-stagger">
                <article class="rb-cap featured" data-tilt>
                    <span class="rb-cap-no">01 / PROCUREMENT</span>
                    <h3>Source the right goods at the right value.</h3>
                    <p>Office, IT, furniture, stationery, humanitarian, construction, maintenance, fuel, automotive, and project supplies.</p>
                    <a class="rb-cap-link" href="{{ route('services') }}">Explore all services <span>↗</span></a>
                </article>
                <article class="rb-cap" data-tilt>
                    <span class="rb-cap-no">02</span>
                    <h3>Transport &amp; freight</h3>
                    <p>Cargo movement, loading, unloading, route planning, and delivery.</p>
                    <span class="rb-cap-arrow">↗</span>
                </article>
                <article class="rb-cap" data-tilt>
                    <span class="rb-cap-no">03</span>
                    <h3>Vehicle rental</h3>
                    <p>Cars, 4x4s, buses, drivers, dispatch, and field missions.</p>
                    <span class="rb-cap-arrow">↗</span>
                </article>
                <article class="rb-cap" data-tilt>
                    <span class="rb-cap-no">04</span>
                    <h3>Storage &amp; distribution</h3>
                    <p>Receiving, inventory, temporary storage, and last-mile delivery.</p>
                    <span class="rb-cap-arrow">↗</span>
                </article>
                <article class="rb-cap" data-tilt>
                    <span class="rb-cap-no">05</span>
                    <h3>Field &amp; technical supply</h3>
                    <p>WASH, safety, construction, maintenance, fuel, and spare parts.</p>
                    <span class="rb-cap-arrow">↗</span>
                </article>
            </div>
        </div>
    </section>

    <!-- ================= PROCESS ================= -->
    <section class="rb-process" aria-labelledby="rb-process-title">
        <div class="rb-shell rb-process-shell">
            <div class="rb-process-head rb-reveal">
                <div>
                    <span class="rb-kicker">The RBLS operating model</span>
                    <h2 id="rb-process-title">Simple for you.<br>Disciplined underneath.</h2>
                </div>
                <p>Our procurement approach keeps requirements, suppliers, quality, movement, and documentation connected from start to finish.</p>
            </div>
            <div class="rb-process-line rb-reveal" aria-hidden="true">
                <span class="rb-process-node"></span>
                <span class="rb-process-node"></span>
                <span class="rb-process-node"></span>
            </div>
            <div class="rb-process-grid rb-stagger">
                <article class="rb-step">
                    <span class="rb-step-marker">01</span>
                    <h3>Understand</h3>
                    <p>We review specifications, quantities, quality standards, budget, destination, and delivery requirements.</p>
                </article>
                <article class="rb-step">
                    <span class="rb-step-marker">02</span>
                    <h3>Coordinate</h3>
                    <p>We source, compare, check, procure, schedule transport, and adapt the plan to the operating environment.</p>
                </article>
                <article class="rb-step">
                    <span class="rb-step-marker">03</span>
                    <h3>Deliver</h3>
                    <p>We move the goods, complete the handover, keep the records, and stay accountable through completion.</p>
                </article>
            </div>
        </div>
    </section>

    <!-- ================= PROOF ================= -->
    <section class="rb-proof" aria-labelledby="rb-proof-title">
        <div class="rb-shell rb-proof-shell">
            <div class="rb-section-head rb-reveal">
                <div>
                    <span class="rb-kicker">Selected experience</span>
                    <h2 id="rb-proof-title">Proof in the<br>portfolio.</h2>
                </div>
                <p>53 assignments across procurement, transport, supply, and field support for organizations operating across Afghanistan.</p>
            </div>
            <div class="rb-proof-grid rb-stagger">
                <article class="rb-case" data-tilt>
                    <span class="rb-case-tag">01 / MAPWO</span>
                    <h3>Field transport across multiple provinces.</h3>
                    <p>Vehicle rental and staff transportation supporting field monitoring and program operations.</p>
                    <div class="rb-case-meta">Kabul · Wardak · Paktika · Bamiyan · Uruzgan · Farah · Badghis</div>
                </article>
                <article class="rb-case" data-tilt>
                    <span class="rb-case-tag">02 / DRC</span>
                    <h3>Procurement that reaches the hard places.</h3>
                    <p>Construction support, office supplies, medicines, firewood, refreshments, and goods transportation.</p>
                    <div class="rb-case-meta">Kabul · Maidan Wardak</div>
                </article>
                <article class="rb-case" data-tilt>
                    <span class="rb-case-tag">03 / NAC</span>
                    <h3>Provincial programs furnished well.</h3>
                    <p>Two Faryab Province cupboard-supply assignments with a combined value of approximately AFN 8.3M.</p>
                    <div class="rb-case-meta">Faryab · 2024</div>
                </article>
            </div>
            <a class="rb-cap-link" href="{{ route('projects') }}" style="margin-top:35px" data-magnetic>View the full portfolio <span>↗</span></a>
        </div>
    </section>

    <!-- ================= CLIENTS ================= -->
    <section class="rb-clients">
        <div class="rb-shell">
            <span class="rb-kicker">Organizations we have supported</span>
            <div class="rb-client-line rb-stagger">
                <span class="rb-client">MAPWO</span>
                <span class="rb-client">Danish Refugee Council</span>
                <span class="rb-client">NAC</span>
                <span class="rb-client">HVSO</span>
                <span class="rb-client">ASPSO</span>
                <span class="rb-client">UN agencies</span>
            </div>
        </div>
    </section>

    <!-- ================= MESSAGE ================= -->
    <section class="rb-message" aria-labelledby="rb-message-title">
        <div class="rb-shell rb-message-grid">
            <div class="rb-president-frame rb-reveal">
                <img src="{{ asset('images/president.png').'?v='.substr(hash_file('sha256', public_path('images/president.png')), 0, 12) }}" alt="Mayel Agha Ahmadi, President of RBLS" loading="lazy">
            </div>
            <div class="rb-reveal-right rb-reveal">
                <span class="rb-kicker">A message from our president</span>
                <h2 class="rb-quote" id="rb-message-title">Effective logistics is more than moving goods. It is creating dependable systems that help organizations deliver results.</h2>
                <p class="rb-message-copy">We listen carefully, respond effectively, and deliver responsibly. Our commitment is to build the trust and consistency that make long-term partnerships possible.</p>
                <div class="rb-signature">
                    <strong>Mayel Agha Ahmadi</strong>
                    <span>President, Roman Bahram Logistic Services</span>
                </div>
            </div>
        </div>
    </section>

    <!-- ================= FINAL ================= -->
    <section class="rb-final">
        <div class="rb-shell">
            <div class="rb-final-card rb-reveal">
                <div>
                    <h2>Have a difficult assignment?</h2>
                    <p>Tell us what you need to source, transport, store, or deliver. We will help you plan a practical next step.</p>
                </div>
                <a class="rb-btn rb-btn-dark" href="{{ route('contact') }}" data-magnetic>
                    <span>Get in touch</span><span aria-hidden="true">↗</span>
                </a>
            </div>
        </div>
    </section>
</div>




@endsection
