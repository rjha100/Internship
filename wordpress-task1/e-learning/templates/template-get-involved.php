<?php
/*
Template Name: Get Involved
*/
get_template_part('parts/header');
?>

<style>
.get-involved-page {
  max-width: var(--max-width);
  margin: 0 auto;
  padding: 40px 20px;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.get-involved-hero {
  text-align: center;
  margin-bottom: 50px;
}

.get-involved-hero h1 {
  font-family: "Montserrat";
  font-size: 48px;
  color: var(--text-color);
  margin-bottom: 20px;
}

.get-involved-hero p {
  font-size: 18px;
  color: #666;
  max-width: 600px;
  margin: 0 auto;
}

.get-involved-options {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 30px;
}

.involvement-option {
  background: #f9f9f9;
  padding: 30px;
  border-radius: 8px;
  text-align: center;
  transition: transform 0.3s ease;
}

.involvement-option:hover {
  transform: translateY(-5px);
}

.involvement-option h3 {
  font-family: "Montserrat";
  font-size: 24px;
  margin-bottom: 15px;
  color: var(--text-color);
}

.involvement-option p {
  color: #666;
  line-height: 1.6;
  margin-bottom: 20px;
}

.involvement-option .involvement-cta {
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

.involvement-option .involvement-cta:hover {
  background: #b52828;
}

.contact-section {
  margin-top: 60px;
  text-align: center;
  padding: 40px;
  background: #f5f5f5;
  border-radius: 8px;
}

.contact-section h2 {
  font-family: "Montserrat";
  font-size: 32px;
  color: var(--text-color);
  margin-bottom: 20px;
}

.contact-section p {
  color: #666;
  margin-bottom: 30px;
}

.contact-form {
  max-width: 500px;
  margin: 0 auto;
}

.contact-form input,
.contact-form textarea {
  width: 100%;
  padding: 12px;
  margin-bottom: 15px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 16px;
}

.contact-form button {
  background: #dc3636;
  color: white;
  padding: 12px 24px;
  border: none;
  border-radius: 4px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.3s ease;
}

.contact-form button:hover {
  background: #b52828;
}

@media (max-width: 768px) {
  .get-involved-options {
    grid-template-columns: 1fr;
  }
}
</style>

<div class="content-wrapper">
  <div class="get-involved-page">
    <div class="get-involved-hero">
      <h1>Get Involved</h1>
      <p>Join our community and make a difference. There are many ways to contribute and get involved with our mission.</p>
    </div>

    <div class="get-involved-options">
      <div class="involvement-option">
        <h3>Volunteer as Instructor</h3>
        <p>Share your expertise by teaching classes and workshops. Help others learn and grow in their careers.</p>
        <a href="#" class="involvement-cta">Apply Now</a>
      </div>
      <div class="involvement-option">
        <h3>Mentor Students</h3>
        <p>Provide guidance and support to students as they navigate their learning journey.</p>
        <a href="#" class="involvement-cta">Become a Mentor</a>
      </div>
      <div class="involvement-option">
        <h3>Partner with Us</h3>
        <p>Collaborate on programs, sponsor events, or provide resources to support our initiatives.</p>
        <a href="#" class="involvement-cta">Partner Inquiry</a>
      </div>
      <div class="involvement-option">
        <h3>Donate</h3>
        <p>Support our mission financially to help us reach more students and expand our programs.</p>
        <a href="#" class="involvement-cta">Make a Donation</a>
      </div>
      <div class="involvement-option">
        <h3>Spread the Word</h3>
        <p>Help us grow our community by sharing our programs with friends, family, and colleagues.</p>
        <a href="#" class="involvement-cta">Share Now</a>
      </div>
      <div class="involvement-option">
        <h3>Event Organizer</h3>
        <p>Help plan and execute our workshops, seminars, and networking events.</p>
        <a href="#" class="involvement-cta">Get Involved</a>
      </div>
    </div>
</div>

<?php get_template_part('parts/footer'); ?>