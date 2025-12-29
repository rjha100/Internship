<?php
/*
Template Name: Programs and Events
*/
get_template_part('parts/header');
?>

<style>
.programs-events-page {
  max-width: var(--max-width);
  margin: 0 auto;
  padding: 40px 20px;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.programs-events-hero {
  text-align: center;
  margin-bottom: 50px;
}

.programs-events-hero h1 {
  font-family: "Montserrat";
  font-size: 48px;
  color: var(--text-color);
  margin-bottom: 20px;
}

.programs-events-hero p {
  font-size: 18px;
  color: #666;
  max-width: 600px;
  margin: 0 auto;
}

.programs-events-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
  gap: 30px;
}

.program-event-item {
  background: #f9f9f9;
  padding: 30px;
  border-radius: 8px;
  transition: transform 0.3s ease;
}

.program-event-item:hover {
  transform: translateY(-5px);
}

.program-event-item h3 {
  font-family: "Montserrat";
  font-size: 24px;
  margin-bottom: 15px;
  color: var(--text-color);
}

.program-event-item .date {
  font-weight: bold;
  color: #dc3636;
  margin-bottom: 10px;
}

.program-event-item p {
  color: #666;
  line-height: 1.6;
  margin-bottom: 15px;
}

.program-event-item .event-cta {
  display: inline-block;
  background: #dc3636;
  color: white;
  padding: 10px 24px;
  border-radius: 4px;
  text-decoration: none;
  font-weight: 700;
  font-size: 16px;
  transition: background 0.3s ease;
  border: none;
  cursor: pointer;
}

.program-event-item .event-cta:hover {
  background: #b52828;
}

@media (max-width: 768px) {
  .programs-events-grid {
    grid-template-columns: 1fr;
  }
}
</style>

<div class="content-wrapper">
  <div class="programs-events-page">
    <div class="programs-events-hero">
      <h1>Programs & Events</h1>
      <p>Join our exciting programs and events to connect, learn, and grow with our community.</p>
    </div>

    <div class="programs-events-grid">
      <div class="program-event-item">
        <h3>Web Development Bootcamp</h3>
        <div class="date">January 15-30, 2025</div>
        <p>Intensive 2-week program covering full-stack web development from basics to advanced concepts.</p>
        <a href="#" class="event-cta">Learn More</a>
      </div>
      <div class="program-event-item">
        <h3>Data Science Workshop</h3>
        <div class="date">February 10, 2025</div>
        <p>One-day workshop on data analysis techniques and machine learning fundamentals.</p>
        <a href="#" class="event-cta">Register</a>
      </div>
      <div class="program-event-item">
        <h3>Networking Mixer</h3>
        <div class="date">March 5, 2025</div>
        <p>Connect with industry professionals and fellow learners at our monthly networking event.</p>
        <a href="#" class="event-cta">RSVP</a>
      </div>
      <div class="program-event-item">
        <h3>UX Design Challenge</h3>
        <div class="date">April 20-25, 2025</div>
        <p>5-day design sprint where teams compete to solve real-world UX problems.</p>
        <a href="#" class="event-cta">Join Team</a>
      </div>
      <div class="program-event-item">
        <h3>Career Development Seminar</h3>
        <div class="date">May 12, 2025</div>
        <p>Learn essential skills for career advancement in tech and creative industries.</p>
        <a href="#" class="event-cta">Sign Up</a>
      </div>
      <div class="program-event-item">
        <h3>Alumni Showcase</h3>
        <div class="date">June 15, 2025</div>
        <p>Celebrate our graduates' achievements and see their portfolio projects.</p>
        <a href="#" class="event-cta">View Event</a>
      </div>
    </div>
  </div>
</div>

<?php get_template_part('parts/footer'); ?>