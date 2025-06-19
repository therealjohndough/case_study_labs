anime({
  targets: '.headline',
  translateY: [-50, 0],
  opacity: [0, 1],
  duration: 1500,
  easing: 'easeOutExpo'
});

anime({
  targets: '.subheadline',
  delay: 500,
  translateY: [-30, 0],
  opacity: [0, 1],
  duration: 1200,
  easing: 'easeOutExpo'
});

anime({
  targets: '.cta',
  delay: 1000,
  scale: [0.8, 1],
  opacity: [0, 1],
  duration: 1000,
  easing: 'easeOutBack'
});
