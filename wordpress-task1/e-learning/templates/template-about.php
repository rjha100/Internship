<?php
/*
Template Name: About
*/
get_template_part('parts/header');
?>

<style>
.about-page {
  max-width: var(--max-width);
  margin: 0 auto;
  padding: 40px 20px;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.about-hero {
  text-align: center;
  margin-bottom: 50px;
}

.about-hero h1 {
  font-family: "Montserrat";
  font-size: 48px;
  color: var(--text-color);
  margin-bottom: 20px;
}

.about-hero p {
  font-size: 18px;
  color: #666;
  max-width: 600px;
  margin: 0 auto;
}

.about-content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 50px;
  margin-bottom: 50px;
}

.about-section h2 {
  font-family: "Montserrat";
  font-size: 32px;
  color: var(--text-color);
  margin-bottom: 20px;
}

.about-section p {
  color: #666;
  line-height: 1.6;
  margin-bottom: 20px;
}

.about-stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 30px;
  text-align: center;
}

.stat-item {
  background: #f9f9f9;
  padding: 30px;
  border-radius: 8px;
}

.stat-number {
  font-family: "Montserrat";
  font-size: 48px;
  font-weight: 900;
  color: #dc3636;
  display: block;
  margin-bottom: 10px;
}

.stat-label {
  font-size: 18px;
  color: var(--text-color);
  font-weight: 600;
}

@media (max-width: 768px) {
  .about-content {
    grid-template-columns: 1fr;
  }
}
</style>

<div class="content-wrapper">
  <div class="about-page">
    <div class="about-hero">
      <h1>About Us</h1>
      <p>We are dedicated to providing high-quality education and fostering a community of lifelong learners.</p>
    </div>

    <div class="about-content">
      <div class="about-section">
        <h2>Our Mission</h2>
        <p>To empower individuals through accessible, innovative education that prepares them for success in an ever-changing world.</p>
        <p>We believe that everyone deserves the opportunity to learn and grow, regardless of their background or circumstances.</p>
      </div>

      <div class="about-section">
        <h2>Our Vision</h2>
        <p>To be the leading platform for transformative learning experiences that inspire creativity, critical thinking, and collaboration.</p>
        <p>Through our diverse range of programs and events, we aim to create a global community of empowered learners.</p>
      </div>
    </div>

    <div class="about-stats">
      <div class="stat-item">
        <span class="stat-number">10,000+</span>
        <span class="stat-label">Students Enrolled</span>
      </div>
      <div class="stat-item">
        <span class="stat-number">50+</span>
        <span class="stat-label">Expert Instructors</span>
      </div>
      <div class="stat-item">
        <span class="stat-number">100+</span>
        <span class="stat-label">Courses Offered</span>
      </div>
      <div class="stat-item">
        <span class="stat-number">95%</span>
        <span class="stat-label">Satisfaction Rate</span>
      </div>
    </div>
  </div>
</div>

<?php get_template_part('parts/footer'); ?>