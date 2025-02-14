import "./bootstrap";

// document
//     .evaluate(
//         "//span[text()='Most relevant']",
//         document,
//         null,
//         XPathResult.FIRST_ORDERED_NODE_TYPE,
//         null
//     )
//     .singleNodeValue.click();

// setTimeout(() => {
//     document
//         .evaluate(
//             "//span[text()='All comments']",
//             document,
//             null,
//             XPathResult.FIRST_ORDERED_NODE_TYPE,
//             null
//         )
//         .singleNodeValue.click();
// }, 1000); // Delay of 1 second

// const comments = Array.from(
//     document.querySelectorAll('div[role="article"]')
// ).filter((div) => div.getAttribute("aria-label")?.startsWith("Comment by"));

// // Extract the comment text
// comments.forEach((comment) => {
//     const textElement = comment.querySelector(
//         'span[dir="auto"][lang] div[dir="auto"]'
//     );

//     if (textElement) {
//         const dynamicText = textElement ? textElement.textContent.trim() : null;
//         console.log("Comment:", dynamicText);

//         // Find all span elements that contain the text 'Follow'
//         const spans = comment.querySelectorAll("span");

//         // Loop through each span and check if it contains the text 'Follow'
//         spans.forEach((span) => {
//             if (span.innerText.trim() === "Follow") {
//                 const div = span.closest("div");
//                 if (div) {
//                     // div.click(); // Click the div
//                 }
//             }
//         });
//     }

//     const ulElement = comment.querySelector("ul");

//     const liElements = ulElement ? ulElement.querySelectorAll("li") : [];

//     liElements.forEach((li, index) => {
//         if (index == 1) {
//             // like 2, for reply
//             const buttonDiv = li.querySelector('div[role="button"]');
//             if (buttonDiv) {
//                 // buttonDiv.click();
//             }
//         }
//     });

//     const links = comment.querySelectorAll("a");

//     links.forEach((link) => {
//         if (link.href.includes("user")) {
//             const innerText = link
//                 ? link.querySelector("span > span")?.textContent.trim()
//                 : null;

//             console.log(innerText); // Output: "inner"
//             console.log(link.href);
//         }
//     });
// });
